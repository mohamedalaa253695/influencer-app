<?php
namespace App\Http\Controllers;

use Auth;
use Cookie;
use App\User;
use Illuminate\Http\Request;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\UpdateInfoRequest;
use App\Http\Requests\UpdatePasswordRequest;
use Symfony\Component\HttpFoundation\Response ;

class AuthController
{
    //

    public function login(Request $request)
    {
        if (Auth::attempt($request->only('email', 'password'))) {
            $user = Auth::user();

            $token = $user->createToken('admin')->accessToken;
            $cookie = cookie('jwt', $token, 3600);

            return response([
                'token' => $token,
            ])->withCookie($cookie);
        }

        return response([
            'error' => 'Invalid Credentials !',
        ], Response::HTTP_UNAUTHORIZED);
    }

    public function logout()
    {
        $cookie = Cookie::forget('jwt');
        return response([
            'message' => 'success'
        ])->withCookie($cookie);
    }

    public function register(RegisterRequest $request)
    {
        $user = User::create($request->only('first_name', 'last_name', 'email') +
        [
            'password' => Hash::make($request->input('password')),
            'is_influencer' => 1,
            'role_id' => 1
        ]);
        return response($user, Response::HTTP_CREATED);
    }

    public function user()
    {
        $user = Auth::user();

        $resource = new UserResource($user);

        if ($user->isInfluencer()) {
            return $resource;
        }

        return $resource->additional([
            'data' => [
                'permission' => $user->permissions()
            ]
        ]);
    }

    /**
     * update user info
     *
     * @param Request $request
     * @return User $user
     */
    public function updateInfo(UpdateInfoRequest $request)
    {
        $user = Auth::user();
        $user->update($request->only('first_name', 'last_name', 'email', 'role_id'));
        return response(new UserResource($user), Response::HTTP_ACCEPTED);
    }

    public function updatePassword(UpdatePasswordRequest $request)
    {
        $user = Auth::user();
        $user->update([
            'password' => Hash::make($request->input('password')),
        ]);
    }
}
