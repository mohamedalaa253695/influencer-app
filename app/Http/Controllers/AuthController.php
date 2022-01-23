<?php
namespace App\Http\Controllers;

use App\Http\Requests\RegisterRequest;
use Auth;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response as HttpFoundationResponse;

class AuthController extends Controller
{
    //

    public function login(Request $request)
    {
        if (Auth::attempt($request->only('email', 'password'))) {
            $user = Auth::user();

            $token = $user->createToken('admin')->accessToken;

            return[
                'token' => $token
            ];
        }

        return response([
            'error' => 'Invalid Credentials !',
        ], HttpFoundationResponse::HTTP_UNAUTHORIZED);
    }

    public function register(RegisterRequest $request)
    {
        $user = User::create($request->only('first_name', 'last_name', 'email') +
        ['password' => Hash::make($request->input('password'))]);
        return response($user, HttpFoundationResponse::HTTP_CREATED);
    }
}
