<?php

namespace App\Repositories\Accout;

use App\Contracts\Repositories\Accout\UserRepositoryInterface;
use App\Models\Accout\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class UserRepository implements UserRepositoryInterface
{
    public function __construct(
       private User $userModel
    ) {}

    public function index(int $perPage = 15, int $page = 1, ?array $filters = []): LengthAwarePaginator
    {
        $query = $this->userModel->with('status');

        if(empty($filters)) return $query->orderByDesc('id')->paginate($perPage);

        if(!empty($filters['name'])){
            $query->where('name','like', '%' . $filters['name'] . '%');
        }
        if(!empty($filters['email'])){
            $query->where('email','like', '%' . $filters['email'] . '%');
        }

     return $query->orderByDesc('id')->paginate($perPage);
    }
    public function show(User $user): User
    {
        // Route model binding já buscou o user por ID — apenas carregamos as relações
        return $user->load('status');
    }
    public function store(array $data)
    {
        return $this->userModel->create($data);
    }
    public function update(User $user, array $data): User
    {
        $user->update($data);

        return $user;
    }
    public function delete(User $user)
    {
        return $user->delete();
    }
}
