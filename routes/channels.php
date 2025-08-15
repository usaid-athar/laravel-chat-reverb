<?php

use Illuminate\Support\Facades\Broadcast;

// Allow users to listen to their own chat channel
Broadcast::channel('chat.{id}', function ($user, $id) {
    \Log::info('Channel authorization attempt', [
        'user_id' => $user->id,
        'channel_id' => $id,
        'authorized' => (int) $user->id === (int) $id
    ]);
    
    return (int) $user->id === (int) $id;
});