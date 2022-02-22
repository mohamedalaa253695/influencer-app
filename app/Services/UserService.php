<?php
namespace App\Services;

use App\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Http;

class UserService
{
    private $endpoint = 'http://172.17.0.1:8000/api';

    public function headers()
    {
        $headers = [];

        if ($jwt = request()->cookie('jwt')) {
            $headers['Authorization'] = "Bearer {$jwt}";
            $headers['content-type'] = 'application/json';
            $headers['Accept'] = 'application/json';
        }

        if (request()->headers->get('Authorization')) {
            $headers['Authorization'] = request()->headers->get('Authorization');
            $headers['content-type'] = 'application/json';
            $headers['Accept'] = 'application/json';
        }

        return $headers;
    }

    public function request()
    {
        return Http::withHeaders($this->headers());
    }

    public function parseUser($json): User
    {
        $user = new User();
        $user->id = $json['id'];
        $user->first_name = $json['first_name'];
        $user->last_name = $json['last_name'];
        $user->email = $json['email'];
        $user->is_influencer = $json['is_influencer'] ?? 0;
        return $user;
    }

    public function getUser(): User
    {
        $json = Http::withHeaders($this->headers())->get("{$this->endpoint}/user")->json();

        return $this->parseUser($json);
    }

    public function isAdmin()
    {
        return $this->request()->get("{$this->endpoint}/admin")->successful();
    }

    public function isInfluencer()
    {
        return $this->request()->get("{$this->endpoint}/influencer")->successful();
    }

    public function allows($ability, $arguments)
    {
        return   Gate::forUser($this->getUser())->authorize($ability, $arguments);
    }

    public function all($page)
    {
        return $this->request()->get("{$this->endpoint}/users?page={$page}")->json();
    }

    public function get($id): User
    {
        $json = $this->request()->get("{$this->endpoint}/users/{$id}")->json();
        return $this->parseUser($json);
    }

    public function create($data)
    {
        $json = $this->request()->get("{$this->endpoint}/users/{$data}")->json();
        return $this->parseUser($json);
    }

    public function update($id, $data)
    {
        $json = $this->request()->put("{$this->endpoint}/users/{$id}", $data)->json();
        return $this->parseUser($json);
    }

    public function delete($id)
    {
        return $this->request()->delete("{$this->endpoint}/users/{$id}")->successful();
    }
}
