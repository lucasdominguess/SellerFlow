<?php

namespace App\Services\Finance;

use App\Contracts\Repositories\Finance\AccountPayableRepositoryInterface;
use App\Contracts\Services\Finance\AccountPayableServiceInterface;
use App\DTOs\Finance\AccountPayableDTO;
use App\DTOs\Finance\AccountPayableResponseDTO;
use App\Enums\CategoryFinance;
use App\Enums\FormPayment;
use App\Enums\OriginType;
use App\Enums\TransactionStatus;
use App\Models\Finance\AccountPayable;
use App\Models\Purchases\Purchase;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class AccountPayableService implements AccountPayableServiceInterface
{
    public function __construct(
        private AccountPayableRepositoryInterface $repository,
    ) {}

    public function index(int $perPage = 15, int $page = 1, ?array $filters = []): LengthAwarePaginator
    {
        $paginator = $this->repository->index($perPage, $page, $filters);

        $paginator->getCollection()->transform(function ($item) {
            return AccountPayableResponseDTO::fromModel($item)->toArray();
        });

        return $paginator;
    }

    public function show(AccountPayable $accountPayable): AccountPayableResponseDTO
    {
        $accountPayable = $this->repository->show($accountPayable);

        return AccountPayableResponseDTO::fromModel($accountPayable);
    }

    public function store(AccountPayableDTO $dto): AccountPayableResponseDTO
    {
        $accountPayable = $this->repository->store($dto->toArray());

        return AccountPayableResponseDTO::fromModel($accountPayable);
    }

    public function update(AccountPayable $accountPayable, AccountPayableDTO $dto): AccountPayableResponseDTO
    {
        $accountPayable = $this->repository->update($accountPayable, $dto->toArray());

        return AccountPayableResponseDTO::fromModel($accountPayable);
    }

    public function delete(AccountPayable $accountPayable)
    {
        return $this->repository->delete($accountPayable);
    }

    public function proccessPurchase(Purchase $compra) : AccountPayable
    {
      $data =[
          'valor' => $compra->valor_total,
            'vencimento' => $compra->data_vencimento ?? null,
            'pago_em' => $compra->data_pagamento ?? null,
            'status' => TransactionStatus::PENDING->value,
            'categoria_financeira_id' => CategoryFinance::ENTRADA->value,
            'forma_pagamento_id' => FormPayment::PIX->value,
            'origem_tipo' => OriginType::COMPRA->value,
            'origem_id' => $compra->id,
            'company_id' => $compra->company_id,
            'observacao' => $compra->observacao,

      ];
    return  $this->repository->store($data);
    }

    // Propaga o status da compra para a(s) conta(s) a pagar vinculada(s).
    // COMPLETED marca pago_em; voltar para PENDING limpa pago_em. 'atrasado' nunca vem da compra.
    public function syncStatusFromPurchase(Purchase $compra): void
    {
        $status = $compra->status;

        $compra->loadMissing('contasPagar');

        $compra->contasPagar->each(function (AccountPayable $conta) use ($status) {
            $data = ['status' => $status->value];

            if ($status === TransactionStatus::COMPLETED) {
                $data['pago_em'] = now()->toDateString();
            } elseif ($status === TransactionStatus::PENDING) {
                $data['pago_em'] = null;
            }

            $this->repository->update($conta, $data);
        });
    }
}
