<?php

namespace App\Repositories;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;


class UserRepository
{


    public function activate($request)
    {

        $fields = $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);
        //return $request;

        $user = User::where('email', $fields['username'])->first();

        if ($user) {
            if ($user->is_active == 0) {
                $user->is_active = 1;
                $user->password = bcrypt($fields['password']);
                $user->save();
            } else {
                return response()->json(['message' => 'Your account is already activated.'], 200);
            }
        } else {
            echo json_encode(['error' => 'Your email address is not available. Please contact the administrator']);
            exit();
        }

    }


}


