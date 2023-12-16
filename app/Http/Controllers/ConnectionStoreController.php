<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserConnection;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ConnectionStoreController extends Controller
{
    public function __invoke(Request $request)
    {
        $this->validate($request, [
            'search' => ['required', 'exists:users,username']
        ]);

        $user = User::query()
            ->where('username', $request->input('search'))
            ->first();

        $alreadyExists = UserConnection::query()
            ->where('user_id', auth()->id())
            ->where('connection_id', $user->id)
            ->exists();

        if ($alreadyExists) {
            throw ValidationException::withMessages([
                'search' => 'You are already connected with this user.'
            ]);
        }

        $userConnection = new UserConnection();
        $userConnection->user_id = auth()->id();
        $userConnection->connection_id = $user->id;
        $userConnection->save();

        return redirect()->back()->with([
            'added' => 'Connection added successfully.'
        ]);
    }
}
