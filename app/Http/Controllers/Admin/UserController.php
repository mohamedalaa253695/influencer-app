<?php
namespace App\Http\Controllers\Admin;

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
        $this->userService->allows('view', 'users');
        return $this->userService->all($request->input('page', 1));
    }

    public function store(UserCreateRequest $request)
    {
        $this->userService->allows('edit', 'users');

        $data = $request->only('first_name', 'last_name', 'email', 'is_influencer') +
                    ['password' => 'password'];

        $user = $this->userService->create($data);

        UserRole::create([
            'user_id' => $user->id,
            'role_id' => $request->input('role_id'),
        ]);
        AdminAdded::dispatch($user->email);
        return response(new UserResource($user), Response::HTTP_CREATED);
    }

    public function show($user)
    {
        $this->userService->allows('view', 'users');
        // Gate::authorize('view', 'users');
        // dd($user);
        $user = $this->userService->get($user);

        return new UserResource($user);
    }

    public function update(UserUpdateRequest $request, $user)
    {
        $this->userService->allows('edit', 'users');
        // dd($user);
        $user = $this->userService->update($user, $request->only('first_name', 'last_name', 'email'));
        // dd($user->id);
        UserRole::where('user_id', $user->id)->delete();

        UserRole::create([
            'user_id' => $user->id,
            'role_id' => $request->input('role_id'),
        ]);

        return response(new UserResource($user), 202);
    }

    public function destroy($user)
    {
        $this->userService->allows('edit', 'users');
        $this->userService->delete($user);

        // dd($user);

        return response(null, HttpResponse::HTTP_NO_CONTENT);
    }
}
