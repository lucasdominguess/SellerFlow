<?php

namespace App\Services\Finance;

use App\Contracts\Repositories\Finance\AccountReceivableRepositoryInterface;
use App\Contracts\Services\Finance\AccountReceivableServiceInterface;
use App\DTOs\Finance\AccountReceivableDTO;
use App\DTOs\Finance\AccountReceivableResponseDTO;
use App\Enums\OriginType;
use App\Enums\TransactionStatus;
use App\Models\Finance\AccountReceivable;
use App\Models\Sales\Venda;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class AccountReceivableService implements AccountReceivableServiceInterface
{
    public function __construct(
        private AccountReceivableRepositoryInterface $repository,
    ) {}

    public function index(int $perPage = 15, int $page = 1, ?array $filters = []): LengthAwarePaginator
    {
        $paginator = $this->repository->index($perPage, $page, $filters);

        $paginator->getCollection()->transform(function ($item) {
            return AccountReceivableResponseDTO::fromModel($item)->toArray();
        });

        return $paginator;
    }

    public function show(AccountReceivable $accountReceivable): AccountReceivableResponseDTO
    {
        $accountReceivable = $this->repository->show($accountReceivable);

        return AccountReceivableResponseDTO::fromModel($accountReceivable);
    }

    public function store(AccountReceivableDTO $dto): AccountReceivableResponseDTO
    {
        $accountReceivable = $this->repository->store($dto->toArray());

        return AccountReceivableResponseDTO::fromModel($accountReceivable);
    }

    public function update(AccountReceivable $accountReceivable, AccountReceivableDTO $dto): AccountReceivableResponseDTO
    {
        $accountReceivable = $this->repository->update($accountReceivable, $dto->toArray());

        return AccountReceivableResponseDTO::fromModel($accountReceivable);
    }

    public function delete(AccountReceivable $accountReceivable)
    {
        return $this->repository->delete($accountReceivable);
    }
    public function proccessSale(Venda $venda) : AccountReceivable
    {
        $data =[
            'valor' => $venda->valor_bruto,
            'previsao_recebimento' => $venda->data_previsao_repasse ?? null,
            'recebido_em' =>  null,
            'status' => TransactionStatus::PENDING->value,
            'origem_tipo' => OriginType::VENDA->value,
            'origem_id' => $venda->id,
            'company_id' => $venda->company_id,
            'store_id' => $venda->store_id,
            'observacao' => $venda->observacao,
        ];
        return $this->repository->store($data);
    }

    // Propaga o status da venda para a conta a receber vinculada.
    // COMPLETED marca recebido_em; voltar para PENDING limpa recebido_em. 'atrasado' nunca vem da venda.
    public function syncStatusFromSale(Venda $venda): void
    {
        $status = $venda->status;

        $venda->loadMissing('contaReceber');

        $conta = $venda->contaReceber;

        if (! $conta) {
            return;
        }

        $data = ['status' => $status->value];

        if ($status === TransactionStatus::COMPLETED) {
            $data['recebido_em'] = now()->toDateString();
        } elseif ($status === TransactionStatus::PENDING) {
            $data['recebido_em'] = null;
        }

        $this->repository->update($conta, $data);
    }
}
