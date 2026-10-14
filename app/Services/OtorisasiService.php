<?php

namespace App\Services;

use App\Jobs\SendWebPush;
use App\Otorisasi\OtorisasiHandler;
use Illuminate\Support\Facades\DB;

/**
 * Permintaan otorisasi lewat push (PWA). Tabel: DBREQOTORISASI, DBPUSHSUBSCRIPTION
 * (lihat database/sql/otorisasi_request.sql). Modul yang didukung: config/otorisasi.php.
 *
 * Hak otorisasi dibaca dari DBFLMENUWEB (HASACCESS + IsOtorisasi1) untuk menu yang
 * href-nya = $menuPath - sumber yang sama dengan GlobalController::getAkses().
 * Multi-level (IsOtorisasi2..5) belum didukung; LEVELOTO selalu 1.
 */
class OtorisasiService
{
    public function isSupported(string $menuPath): bool
    {
        return array_key_exists($menuPath, config('otorisasi', []));
    }

    public function label(string $menuPath): string
    {
        return config("otorisasi.$menuPath.label", $menuPath);
    }

    public function handler(string $menuPath): OtorisasiHandler
    {
        return app(config("otorisasi.$menuPath.handler"));
    }

    public function kodeMenu(string $menuPath): ?string
    {
        $res = DB::connection('SML')->select('select top 1 KODEMENU from DBMENUWEB where href = :path', ['path' => $menuPath]);

        return $res ? $res[0]->KODEMENU : null;
    }

    public function canApprove(string $username, string $menuPath): bool
    {
        $res = DB::connection('SML')->select('
            select top 1 1 ok
            from DBMENUWEB m
            join DBFLMENUWEB f on f.L1 = m.KODEMENU
            where m.href = :path and f.USERID = :username and f.HASACCESS = 1 and f.IsOtorisasi1 = 1
        ', ['path' => $menuPath, 'username' => $username]);

        return (bool) $res;
    }

    /** USERID semua yang berhak otorisasi menu ini. */
    public function approvers(string $menuPath): array
    {
        $res = DB::connection('SML')->select('
            select distinct f.USERID
            from DBMENUWEB m
            join DBFLMENUWEB f on f.L1 = m.KODEMENU
            where m.href = :path and f.HASACCESS = 1 and f.IsOtorisasi1 = 1
        ', ['path' => $menuPath]);

        return array_map(fn ($r) => $r->USERID, $res);
    }

    public function find(int $id): ?object
    {
        $res = DB::connection('SML')->select('select * from DBREQOTORISASI where ID = :id', ['id' => $id]);

        return $res[0] ?? null;
    }

    /**
     * @return array{status: string, request: ?object}
     *                                                 status: created | exists (masih pending) | approved (sudah diotorisasi) | notfound
     */
    public function createRequest(string $menuPath, string $nobukti, string $username): array
    {
        $handler = $this->handler($menuPath);

        if ($handler->isApproved($nobukti)) {
            return ['status' => 'approved', 'request' => null];
        }

        $kodemenu = $this->kodeMenu($menuPath);
        if (! $kodemenu || ! $handler->summary($nobukti)) {
            return ['status' => 'notfound', 'request' => null];
        }

        $existing = DB::connection('SML')->select("
            select top 1 * from DBREQOTORISASI
            where MENUPATH = :path and NOBUKTI = :nobukti and STATUS = 'P'
            order by ID desc
        ", ['path' => $menuPath, 'nobukti' => $nobukti]);

        if ($existing) {
            return ['status' => 'exists', 'request' => $existing[0]];
        }

        $inserted = DB::connection('SML')->select("
            insert into DBREQOTORISASI (KODEMENU, MENUPATH, NOBUKTI, LEVELOTO, REQUSER, TGLREQ, STATUS, REQREAD)
            output inserted.ID
            values (:kodemenu, :path, :nobukti, 1, :username, getdate(), 'P', 0)
        ", ['kodemenu' => $kodemenu, 'path' => $menuPath, 'nobukti' => $nobukti, 'username' => $username]);

        $request = $this->find((int) $inserted[0]->ID);

        $approvers = array_values(array_diff($this->approvers($menuPath), [$username]));
        $this->notify($approvers, [
            'title' => 'Permintaan Otorisasi '.$this->label($menuPath),
            'body' => $username.' meminta otorisasi '.$nobukti,
            'url' => url('/otorisasi/'.$request->ID),
            'tag' => 'oto-'.$request->ID,
        ]);

        return ['status' => 'created', 'request' => $request];
    }

    /**
     * @return array{ok: bool, message: string}
     */
    public function approve(int $id, string $username): array
    {
        $req = $this->find($id);
        if (! $req) {
            return ['ok' => false, 'message' => 'Permintaan tidak ditemukan'];
        }
        if (! $this->canApprove($username, $req->MENUPATH)) {
            return ['ok' => false, 'message' => 'Anda tidak berhak otorisasi transaksi ini'];
        }

        $handler = $this->handler($req->MENUPATH);

        $result = DB::connection('SML')->transaction(function () use ($req, $username, $handler) {
            // Klaim permintaan dulu - approver kedua yang menekan bersamaan akan dapat 0 baris.
            $claimed = DB::connection('SML')->update("
                update DBREQOTORISASI set STATUS = 'A', ACTUSER = :username, TGLACT = getdate(), REQREAD = 0
                where ID = :id and STATUS = 'P'
            ", ['username' => $username, 'id' => $req->ID]);

            if ($claimed === 0) {
                return ['ok' => false, 'message' => 'Permintaan ini sudah diproses'];
            }

            if ($handler->isApproved($req->NOBUKTI)) {
                // Sudah diotorisasi langsung dari halaman transaksi oleh orang lain.
                DB::connection('SML')->update("
                    update DBREQOTORISASI set STATUS = 'C', CATATAN = 'Sudah diotorisasi langsung' where ID = :id
                ", ['id' => $req->ID]);

                return ['ok' => false, 'message' => 'Transaksi sudah diotorisasi sebelumnya'];
            }

            $handler->approve($req->NOBUKTI, $username);

            return ['ok' => true, 'message' => 'Berhasil otorisasi'];
        });

        if ($result['ok']) {
            $this->notifyRequester($req, $username, 'disetujui');
        }

        return $result;
    }

    /**
     * @return array{ok: bool, message: string}
     */
    public function reject(int $id, string $username, string $catatan): array
    {
        $req = $this->find($id);
        if (! $req) {
            return ['ok' => false, 'message' => 'Permintaan tidak ditemukan'];
        }
        if (! $this->canApprove($username, $req->MENUPATH)) {
            return ['ok' => false, 'message' => 'Anda tidak berhak otorisasi transaksi ini'];
        }

        $claimed = DB::connection('SML')->update("
            update DBREQOTORISASI set STATUS = 'R', ACTUSER = :username, TGLACT = getdate(), CATATAN = :catatan, REQREAD = 0
            where ID = :id and STATUS = 'P'
        ", ['username' => $username, 'catatan' => mb_substr($catatan, 0, 255), 'id' => $req->ID]);

        if ($claimed === 0) {
            return ['ok' => false, 'message' => 'Permintaan ini sudah diproses'];
        }

        $this->notifyRequester($req, $username, 'ditolak: '.$catatan);

        return ['ok' => true, 'message' => 'Permintaan ditolak'];
    }

    /**
     * Dipanggil saat transaksi diotorisasi langsung dari halaman transaksinya (mis.
     * KasController::spOtorisasi), supaya permintaan yang masih pending ikut tertutup.
     */
    public function closePending(string $menuPath, string $nobukti, string $username): void
    {
        $pending = DB::connection('SML')->select("
            select * from DBREQOTORISASI where MENUPATH = :path and NOBUKTI = :nobukti and STATUS = 'P'
        ", ['path' => $menuPath, 'nobukti' => $nobukti]);

        if (! $pending) {
            return;
        }

        DB::connection('SML')->update("
            update DBREQOTORISASI set STATUS = 'A', ACTUSER = :username, TGLACT = getdate(), REQREAD = 0
            where MENUPATH = :path and NOBUKTI = :nobukti and STATUS = 'P'
        ", ['username' => $username, 'path' => $menuPath, 'nobukti' => $nobukti]);

        foreach ($pending as $req) {
            $this->notifyRequester($req, $username, 'disetujui');
        }
    }

    /** Permintaan pending yang bisa diproses user ini. */
    public function pendingFor(string $username): array
    {
        return DB::connection('SML')->select("
            select r.*
            from DBREQOTORISASI r
            join DBFLMENUWEB f on f.L1 = r.KODEMENU and f.USERID = :username
            where r.STATUS = 'P' and f.HASACCESS = 1 and f.IsOtorisasi1 = 1
            order by r.TGLREQ desc
        ", ['username' => $username]);
    }

    /** Permintaan yang dibuat user ini (100 terakhir). */
    public function requestsBy(string $username): array
    {
        return DB::connection('SML')->select('
            select top 100 * from DBREQOTORISASI where REQUSER = :username order by TGLREQ desc
        ', ['username' => $username]);
    }

    /** Angka badge lonceng: pending untuk saya + hasil permintaan saya yang belum dibaca. */
    public function badgeCount(string $username): array
    {
        $res = DB::connection('SML')->select("
            select
              (select count(*) from DBREQOTORISASI r
                 join DBFLMENUWEB f on f.L1 = r.KODEMENU and f.USERID = :u1
                 where r.STATUS = 'P' and f.HASACCESS = 1 and f.IsOtorisasi1 = 1) pending,
              (select count(*) from DBREQOTORISASI
                 where REQUSER = :u2 and STATUS <> 'P' and REQREAD = 0) hasil
        ", ['u1' => $username, 'u2' => $username]);

        return ['pending' => (int) $res[0]->pending, 'hasil' => (int) $res[0]->hasil];
    }

    public function markResultsRead(string $username): void
    {
        DB::connection('SML')->update("
            update DBREQOTORISASI set REQREAD = 1 where REQUSER = :username and STATUS <> 'P' and REQREAD = 0
        ", ['username' => $username]);
    }

    private function notifyRequester(object $req, string $actor, string $hasil): void
    {
        $this->notify([$req->REQUSER], [
            'title' => 'Otorisasi '.$this->label($req->MENUPATH).' '.$req->NOBUKTI,
            'body' => 'Permintaan Anda '.$hasil.' (oleh '.$actor.')',
            'url' => url('/otorisasiinbox?tab=saya'),
            'tag' => 'oto-hasil-'.$req->ID,
        ]);
    }

    private function notify(array $userIds, array $payload): void
    {
        if ($userIds) {
            SendWebPush::dispatch($userIds, $payload);
        }
    }
}
