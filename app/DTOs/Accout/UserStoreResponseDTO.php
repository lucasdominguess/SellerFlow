<?php

namespace App\DTOs\Accout;

use App\Models\Accout\UserStore;

class UserStoreResponseDTO
{
    public function __construct(
       public readonly int $id,
       public readonly int $user_id,
       public readonly int $store_id,
       public readonly int $status_id,
       public readonly int $role_id,
       public readonly UserResponseDTO $user,
       public readonly StoreResponseDTO $store,
    ) {}

    public static function fromModel(UserStore $model): self
    {
        return new self(
            id: $model->id,
            user_id: $model->user_id,
            store_id: $model->store_id,
            status_id: $model->status_id,
            role_id: $model->role_id,
            user: UserResponseDTO::fromModel($model->user),
            store: StoreResponseDTO::fromModel($model->store),
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'store_id' => $this->store_id,
            'status_id' => $this->status_id,
            'role_id' => $this->role_id,
            'user' => $this->user->toArray(),
            'store' => $this->store->toArray(),
        ];
    }
}
