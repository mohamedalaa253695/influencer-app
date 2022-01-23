<?php
namespace App\Http\Controllers;

use App\Http\Requests\UserCreateRequest;
use App\Http\Requests\UserUpdateRequest;
use App\User;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response as HttpFoundationResponse;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //

        return User::paginate(3);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(UserCreateRequest $request)
    {
        $user = User::create($request->only('first_name', 'last_name', 'email') +
                    ['password' => Hash::make('password')]);
        return response($user, HttpFoundationResponse::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        //
        return User::find($user);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(UserUpdateRequest $request, User $user)
    {
        $user = User::find($user->id);
        // dd($user);
        $user->update($request->only('first_name', 'last_name', 'email'));

        return response($user, 202);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        //

        User::destroy($user->id);

        return response(null, 204);
    }
}
