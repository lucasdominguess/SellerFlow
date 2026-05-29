<?php

namespace App\DTOs\Purchases;

use App\Classes\AuthContext;

class CompraDTO
{
    public function __construct(
        public readonly ?int $company_id,
        public readonly ?int $store_id,
        public readonly ?int $user_id,
        public readonly ?int $fornecedor_id,
        public readonly ?int $forma_pagamento_id,
        public readonly ?int $status_id,
        public readonly ?string $numero_nota,
        public readonly ?string $data_compra,
        public readonly ?float $valor_total,
        public readonly ?int $numero_parcelas,
        public readonly ?string $observacao,
        // CompraItemDTO[] — presente apenas no create; update não altera itens por este fluxo
        public readonly array $itens = [],
    ) {}

    // CREATE: identidade (empresa + loja + autor) vem do JWT, nunca do cliente.
    public static function fromCreateRequest(array $data): self
    {
        return new self(
            company_id:        AuthContext::companyIds()->first(),
            store_id:          AuthContext::storeIds()->first(),
            user_id:           AuthContext::userId(),
            fornecedor_id:     (int) $data['fornecedor_id'],
            forma_pagamento_id: (int) $data['forma_pagamento_id'],
            status_id:         (int) $data['status_id'],
            numero_nota:       $data['numero_nota'] ?? null,
            data_compra:       $data['data_compra'] ?? null,
            valor_total:       isset($data['valor_total']) ? (float) $data['valor_total'] : null,
            numero_parcelas:   isset($data['numero_parcelas']) ? (int) $data['numero_parcelas'] : null,
            observacao:        $data['observacao'] ?? null,
            itens: array_map(
                fn(array $item) => CompraItemDTO::fromArray($item),
                $data['itens'] ?? []
            ),
        );
    }

    // UPDATE: identidade é imutável — company_id, store_id, user_id ficam null
    // e somem no array_filter, então jamais são sobrescritos.
    public static function fromUpdateRequest(array $data): self
    {
        return new self(
            company_id:        null,
            store_id:          null,
            user_id:           null,
            fornecedor_id:     isset($data['fornecedor_id']) ? (int) $data['fornecedor_id'] : null,
            forma_pagamento_id: isset($data['forma_pagamento_id']) ? (int) $data['forma_pagamento_id'] : null,
            status_id:         isset($data['status_id']) ? (int) $data['status_id'] : null,
            numero_nota:       $data['numero_nota'] ?? null,
            data_compra:       $data['data_compra'] ?? null,
            valor_total:       isset($data['valor_total']) ? (float) $data['valor_total'] : null,
            numero_parcelas:   isset($data['numero_parcelas']) ? (int) $data['numero_parcelas'] : null,
            observacao:        $data['observacao'] ?? null,
        );
    }

    // Retorna apenas os campos presentes (não-null), evitando sobrescrever dados no update.
    // itens NÃO entra no array: é relação, não coluna da tabela compras.
    public function toArray(): array
    {
        return array_filter([
            'company_id'        => $this->company_id,
            'store_id'          => $this->store_id,
            'user_id'           => $this->user_id,
            'fornecedor_id'     => $this->fornecedor_id,
            'forma_pagamento_id' => $this->forma_pagamento_id,
            'status_id'         => $this->status_id,
            'numero_nota'       => $this->numero_nota,
            'data_compra'       => $this->data_compra,
            'valor_total'       => $this->valor_total,
            'numero_parcelas'   => $this->numero_parcelas,
            'observacao'        => $this->observacao,
        ], fn($value) => !is_null($value));
    }
}
