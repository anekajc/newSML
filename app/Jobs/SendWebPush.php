<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Minishlink\WebPush\Subscription;
use Minishlink\WebPush\WebPush;

/**
 * Kirim Web Push ke semua perangkat (DBPUSHSUBSCRIPTION) milik USERID yang diberikan.
 * Payload dibaca public/sw.js: {title, body, url, tag}.
 */
class SendWebPush implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    public function __construct(public array $userIds, public array $payload) {}

    public function handle(): void
    {
        $vapid = config('services.webpush');
        if (empty($vapid['public_key']) || empty($vapid['private_key'])) {
            Log::warning('SendWebPush: VAPID_PUBLIC_KEY/VAPID_PRIVATE_KEY belum di-set, push dilewati.');

            return;
        }

        $placeholders = implode(',', array_fill(0, count($this->userIds), '?'));
        $subs = DB::connection('SML')->select(
            "select ENDPOINT, P256DH, AUTHKEY from DBPUSHSUBSCRIPTION where USERID in ($placeholders)",
            array_values($this->userIds)
        );

        if (! $subs) {
            return;
        }

        $webPush = new WebPush(['VAPID' => [
            'subject' => $vapid['subject'],
            'publicKey' => $vapid['public_key'],
            'privateKey' => $vapid['private_key'],
        ]]);

        $body = json_encode($this->payload);
        foreach ($subs as $s) {
            $webPush->queueNotification(Subscription::create([
                'endpoint' => $s->ENDPOINT,
                'publicKey' => $s->P256DH,
                'authToken' => $s->AUTHKEY,
            ]), $body);
        }

        foreach ($webPush->flush() as $report) {
            if ($report->isSubscriptionExpired()) {
                // Browser sudah mencabut langganan (404/410) - buang supaya tidak dicoba terus.
                DB::connection('SML')->delete('delete from DBPUSHSUBSCRIPTION where ENDPOINT = ?', [$report->getEndpoint()]);
            } elseif (! $report->isSuccess()) {
                Log::warning('SendWebPush gagal: '.$report->getReason(), ['endpoint' => $report->getEndpoint()]);
            }
        }
    }
}
