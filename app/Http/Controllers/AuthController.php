<?php
namespace App\Http\Controllers;

use App\Services\UserService;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Request;

class AuthController
{
    private $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function user(Request $request)
    {
        $user = $this->userService->getUser();
        $resource = new UserResource($user);

        if ($user->isInfluencer()) {
            return $resource->additional([
                'data' => [
                    'revenue' => $user->revenue(),
                ],
            ]);
        }
        // dd($user->role());

        return $resource->additional([
            'data' => [
                'permissions' => $user->permissions(),
                'role' => $user->role(),

            ],
        ]);
    }
}
