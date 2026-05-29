<?php

namespace App\Repositories\Accout;

use App\Contracts\Repositories\Accout\UserRepositoryInterface;
use App\Enums\Roles;
use App\Enums\Status;
use App\Models\Accout\User;
use App\Models\ListSuspended\Company;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class UserRepository implements UserRepositoryInterface
{
    public function __construct(
       private User $userModel,
       private Company $company

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
    public function findByEmail(string $email): ?User
    {
        return $this->userModel->where('email', $email)->first();
    }

    public function show(User $user): User
    {
        // Route model binding já buscou o user por ID — apenas carregamos as relações
        return $user->load('status');
    }
    public function store(array $dataUser, ?array $dataCompany = null): User
    {
        return DB::transaction(function () use ($dataUser, $dataCompany) {
            $user = $this->userModel->create($dataUser);

            if ($dataCompany) {
                $company = Company::create($dataCompany);
                $user->companyUsers()->create([
                    'company_id' => $company->id,
                    'role_id'    => Roles::USER->value,
                    'status_id'  => Status::PENDING->value,
                ]);


            }

            return $user;
        });
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
    public function getDataUser(User $user): User
    {
        return $user->load('companyUsers.company', 'companyUsers.role', 'companyUsers.status');
    }


}
