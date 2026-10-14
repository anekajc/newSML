<?php

namespace App\Http\Controllers;

use App\Services\OtorisasiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * "Minta Otorisasi" + notifikasi Web Push (PWA). Logika ada di App\Services\OtorisasiService;
 * controller ini hanya HTTP-nya. Rute: routes/otorisasi.php.
 */
class OtorisasiRequestController extends Controller
{
    public function __construct(private OtorisasiService $oto) {}

    // POST /otorisasirequest  (menu = DBMENUWEB.href, mis. 'kas'; nobukti)
    public function spRequest(Request $req)
    {
        $req->validate(['menu' => 'required|string', 'nobukti' => 'required|string']);

        if (! $this->oto->isSupported($req->menu)) {
            return response()->json(['ok' => false, 'message' => 'Menu ini belum mendukung permintaan otorisasi'], 422);
        }

        $username = \Auth::user()->username;
        if ($this->oto->canApprove($username, $req->menu)) {
            return response()->json(['ok' => false, 'message' => 'Anda berhak otorisasi - lakukan otorisasi langsung'], 422);
        }

        $res = $this->oto->createRequest($req->menu, $req->nobukti, $username);

        return match ($res['status']) {
            'created' => response()->json(['ok' => true, 'message' => 'Permintaan otorisasi terkirim ke approver']),
            'exists' => response()->json(['ok' => true, 'message' => 'Permintaan sudah dikirim oleh '.$res['request']->REQUSER
                .' pada '.date('d-m-Y H:i', strtotime($res['request']->TGLREQ)).', masih menunggu approver']),
            'approved' => response()->json(['ok' => false, 'message' => 'Transaksi sudah diotorisasi'], 422),
            default => response()->json(['ok' => false, 'message' => 'Data tidak ditemukan'], 404),
        };
    }

    // GET /otorisasi/{id} - halaman approve mobile (dibuka dari notifikasi)
    public function show(int $id)
    {
        $request = $this->oto->find($id);
        if (! $request || ! $this->oto->isSupported($request->MENUPATH)) {
            abort(404);
        }

        $username = \Auth::user()->username;
        $canApprove = $this->oto->canApprove($username, $request->MENUPATH);

        // Selain approver, hanya peminta sendiri yang boleh melihat isinya.
        if (! $canApprove && $request->REQUSER !== $username) {
            abort(403);
        }

        return view('otorisasi.approve', [
            'request' => $request,
            'label' => $this->oto->label($request->MENUPATH),
            'summary' => $this->oto->handler($request->MENUPATH)->summary($request->NOBUKTI),
            'canApprove' => $canApprove,
        ]);
    }

    // POST /otorisasi/{id}/approve
    public function spApprove(int $id)
    {
        $res = $this->oto->approve($id, \Auth::user()->username);

        return response()->json($res, $res['ok'] ? 200 : 422);
    }

    // POST /otorisasi/{id}/reject  (catatan)
    public function spReject(Request $req, int $id)
    {
        $req->validate(['catatan' => 'required|string|max:255']);

        $res = $this->oto->reject($id, \Auth::user()->username, $req->catatan);

        return response()->json($res, $res['ok'] ? 200 : 422);
    }

    // GET /otorisasiinbox
    public function index()
    {
        return view('otorisasi.inbox', [
            'periode' => app('App\Http\Controllers\GlobalController')->getPeriode(),
        ]);
    }

    // GET /otorisasiinboxloadall
    public function loadAll()
    {
        $username = \Auth::user()->username;

        $map = fn ($r) => [
            'id' => $r->ID,
            'menu' => $this->oto->label($r->MENUPATH),
            'nobukti' => $r->NOBUKTI,
            'requser' => $r->REQUSER,
            'tglreq' => $r->TGLREQ ? date('d-m-Y H:i', strtotime($r->TGLREQ)) : '',
            'status' => $r->STATUS,
            'actuser' => $r->ACTUSER,
            'tglact' => $r->TGLACT ? date('d-m-Y H:i', strtotime($r->TGLACT)) : '',
            'catatan' => $r->CATATAN,
            'unread' => $r->STATUS !== 'P' && ! $r->REQREAD,
        ];

        $res = [
            'pending' => array_map($map, $this->oto->pendingFor($username)),
            'mine' => array_map($map, $this->oto->requestsBy($username)),
        ];

        // Hasil permintaan saya dianggap terbaca begitu daftar ini dimuat.
        $this->oto->markResultsRead($username);

        return $res;
    }

    // GET /otorisasicount - badge lonceng di header
    public function count()
    {
        return $this->oto->badgeCount(\Auth::user()->username);
    }

    // POST /pushsubscribe  (endpoint, keys[p256dh], keys[auth])
    public function spSubscribe(Request $req)
    {
        $req->validate([
            'endpoint' => 'required|url|max:500',
            'keys.p256dh' => 'required|string|max:255',
            'keys.auth' => 'required|string|max:255',
        ]);

        // Satu endpoint = satu browser; kalau perangkatnya ganti user, pindahkan pemiliknya.
        DB::connection('SML')->transaction(function () use ($req) {
            DB::connection('SML')->delete('delete from DBPUSHSUBSCRIPTION where ENDPOINT = :endpoint', ['endpoint' => $req->endpoint]);
            DB::connection('SML')->insert('
                insert into DBPUSHSUBSCRIPTION (USERID, ENDPOINT, P256DH, AUTHKEY, USERAGENT, TGLBUAT)
                values (:userid, :endpoint, :p256dh, :authkey, :useragent, getdate())
            ', [
                'userid' => \Auth::user()->username,
                'endpoint' => $req->endpoint,
                'p256dh' => $req->input('keys.p256dh'),
                'authkey' => $req->input('keys.auth'),
                'useragent' => mb_substr((string) $req->userAgent(), 0, 255),
            ]);
        });

        return ['ok' => true];
    }

    // POST /pushunsubscribe  (endpoint)
    public function spUnsubscribe(Request $req)
    {
        $req->validate(['endpoint' => 'required|string']);

        DB::connection('SML')->delete('delete from DBPUSHSUBSCRIPTION where ENDPOINT = :endpoint and USERID = :userid', [
            'endpoint' => $req->endpoint,
            'userid' => \Auth::user()->username,
        ]);

        return ['ok' => true];
    }
}
