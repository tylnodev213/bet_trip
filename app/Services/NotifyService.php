<?php

namespace App\Services;

use App\Models\Admin;
use App\Notifications\NewTourNotification;
use Pusher\Pusher;


class NotifyService
{
    public static function sendNotifyToAdmin($dataNotification)
    {
        $admin = Admin::find(1);
        $admin->notify(new NewTourNotification($dataNotification));
        $latestNotification = $admin->notifications()->latest()->first();
        $dataNotification['url'] .= '?notification_id=' . $latestNotification->id;
        $options = array(
            'cluster' => 'ap1',
            'encrypted' => true
        );

        $pusher = new Pusher(
            env('PUSHER_APP_KEY'),
            env('PUSHER_APP_SECRET'),
            env('PUSHER_APP_ID'),
            $options
        );

        $pusher->trigger('NotificationEvent', 'send-message', $dataNotification);
    }
}
