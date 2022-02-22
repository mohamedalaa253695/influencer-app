<?php
namespace App\Http\Controllers\Admin;

use App\User;
use App\UserRole;
use App\Jobs\AdminAdded;
use Illuminate\Http\Request;
use App\Services\UserService;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Gate;
// use App\Providers\AuthServiceProvider;

use App\Http\Requests\UserCreateRequest;
// use Request;
use App\Http\Requests\UserUpdateRequest;
use Illuminate\Http\Response as HttpResponse;
use Symfony\Component\HttpFoundation\Response;

class UserController extends Controller
{
    private $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function index(Request $request)
    {
        // return User::with('role')->paginate(3);
        $this->userService->allows('view', 'users');
        return $this->userService->all($request->input('page', 1));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(UserCreateRequest $request)
    {
        $this->userService->allows('edit', 'users');
        $data = User::create($request->only('first_name', 'last_name', 'email', 'is_influencer') +
                    ['password' => 'password']);

        $user = $this->userService->create($data);

        UserRole::create([
            'user_id' => $user->id,
            'role_id' => $request->input('role_id'),
        ]);
        AdminAdded::dispatch($user->email);
        return response(new UserResource($user), Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        $this->userService->allows('view', 'users');
        // Gate::authorize('view', 'users');
        // dd($user->id);
        $user = $this->userService->get($user->id);

        return new UserResource($user);
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
        $this->userService->allows('edit', 'users');
        $user = $this->userService->update($user->id, $request->only('first_name', 'last_name', 'email'));

        // $user = User::find($user->id);
        // $user->update($request->only('first_name', 'last_name', 'email'));
        UserRole::where('user_id', $user->id)->delete();

        UserRole::create([
            'user_id' => $user->id,
            'role_id' => $request->input('role_id'),
        ]);

        return response(new UserResource($user), 202);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        $this->userService->allows('edit', 'users');
        $this->userService->delete($user->id);

        // dd($user);

        return response(null, HttpResponse::HTTP_NO_CONTENT);
    }
}
