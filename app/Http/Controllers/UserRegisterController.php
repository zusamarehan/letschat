<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserRegisterController extends Controller
{
    public function __invoke(Request $request)
    {
        $this->validate($request, [
            'username' => ['string', 'required', 'unique:users'],
            'password' => ['string', 'required']
        ]);

        $user = new User();
        $user->username = $request->input('username');
        $user->password = bcrypt($request->input('password'));
        $user->save();

        return redirect()->route('chat');
    }
}
