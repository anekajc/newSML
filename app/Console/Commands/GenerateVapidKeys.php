<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Minishlink\WebPush\VAPID;

class GenerateVapidKeys extends Command
{
    protected $signature = 'webpush:vapid';

    protected $description = 'Buat pasangan kunci VAPID untuk Web Push (salin hasilnya ke .env)';

    public function handle(): int
    {
        try {
            $keys = VAPID::createVapidKeys();
        } catch (\RuntimeException $e) {
            // Di Windows (Laragon/XAMPP) OpenSSL sering tidak menemukan openssl.cnf. Harus
            // di-set sebagai environment variable SEBELUM PHP jalan (putenv() tidak cukup) -
            // berlaku juga untuk web server & queue worker, karena tiap push membuat kunci EC baru.
            $this->error('Gagal membuat kunci EC: '.$e->getMessage());
            $this->line('Set environment variable OPENSSL_CONF ke openssl.cnf bawaan PHP, mis.:');
            $this->line('  '.dirname(PHP_BINARY).DIRECTORY_SEPARATOR.'extras'.DIRECTORY_SEPARATOR.'ssl'.DIRECTORY_SEPARATOR.'openssl.cnf');

            return self::FAILURE;
        }

        $this->line('Tambahkan ke .env (sekali saja - mengganti kunci membuat semua langganan lama tidak berlaku):');
        $this->newLine();
        $this->line('VAPID_PUBLIC_KEY='.$keys['publicKey']);
        $this->line('VAPID_PRIVATE_KEY='.$keys['privateKey']);
        $this->line('VAPID_SUBJECT=mailto:admin@example.com');

        return self::SUCCESS;
    }
}
