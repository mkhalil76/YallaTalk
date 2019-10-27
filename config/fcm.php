<?php

return [
    'driver' => env('FCM_PROTOCOL', 'http'),
    'log_enabled' => false,

    'http' => [
        'server_key' => env('FCM_SERVER_KEY', 'AAAAMNPq8ZY:APA91bFtbSaUW51tItTX-mEwzp7BcqVFdxJXiLMlmlKH417ey997RXwGZdYxD_0NogHZV0kUOEJ8RdAsIkdsfReh7pdr5UjyUkmbW67GkBuEwPJANtULiulI5PI3mC62kSoxpEfg-tuD'),
        'sender_id' => env('FCM_SENDER_ID', '209713820054'),
        'server_send_url' => 'https://fcm.googleapis.com/fcm/send',
        'server_group_url' => 'https://android.googleapis.com/gcm/notification',
        'timeout' => 30.0, // in second
    ],
];
