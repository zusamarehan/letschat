<?php

namespace App\Http\Controllers;

use App\Models\Message;
use Illuminate\Http\Request;

class ConnectionMessageIndexController extends Controller
{
    public function __invoke(Request $request): array
    {
        $this->validate($request, [
            'connection_id' => ['required', 'string', 'exists:users,id']
        ]);

        $received = Message::query()
            ->where('receiver_id', auth()->user()->id)
            ->where('sender_id', $request->input('connection_id'));

        $sent = Message::query()
            ->where('receiver_id', $request->input('connection_id'))
            ->where('sender_id', auth()->user()->id);

        return $received->unionAll($sent)->orderBy('created_at')->get()->toArray();
    }
}
