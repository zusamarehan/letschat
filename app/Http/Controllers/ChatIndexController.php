<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserConnection;

class ChatIndexController extends Controller
{
    public function __invoke()
    {
        $connections = User::query()
            ->whereIn('id', UserConnection::query()
                ->where('user_id', auth()->id())
                ->pluck('connection_id'))
            ->get();

        return view('chat', [
            'connections' => $connections
        ]);
    }
}
