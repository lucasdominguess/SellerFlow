<?php

namespace App\DTOs\Sales;

use App\Classes\AuthContext;
use App\Enums\Status;
use App\DTOs\Sales\VendaItemDTO;

class VendasDTO
{
    public function __construct(
        public readonly ?int $company_id,
        public readonly ?int $store_id,
        public readonly ?int $user_id,
        public readonly ?int $market_place_id,
        public readonly ?string $numero_pedido,
        public readonly ?string $data_venda,
        public readonly ?float $valor_bruto,
        public readonly ?float $taxa_marketplace,
        public readonly ?float $valor_frete,
        public readonly ?string $data_previsao_repasse,
        public readonly ?string $observacao,
        public readonly ?int $status_id = null,
        // VendaItemDTO[] — presente apenas no create; update não altera itens por este fluxo
        public readonly array $venda_itens = [],
    ) {}

    // CREATE: identidade (empresa + loja + autor) vem do JWT, nunca do cliente.
    // auth() aqui é aceitável (exceção do fluxo canônico para injetar o usuário autenticado).
    public static function fromCreateRequest(array $data): self
    {
        return new self(
            company_id:      $data['company_id'],
            store_id:        $data['store_id'],
            user_id:         $data['user_id'],
            market_place_id: $data['market_place_id'] ?? null,
            numero_pedido:   $data['numero_pedido'] ?? null,
            data_venda:      $data['data_venda'] ?? null,
            valor_bruto:      isset($data['valor_bruto']) ? (float) $data['valor_bruto'] : null,
            taxa_marketplace: isset($data['taxa_marketplace']) ? (float) $data['taxa_marketplace'] : null,
            valor_frete:      isset($data['valor_frete']) ? (float) $data['valor_frete'] : null,
            data_previsao_repasse: $data['data_previsao_repasse'] ?? null,
            observacao:            $data['observacao'] ?? null,
            status_id:             $data['status_id'] ?? Status::ACTIVE->value, // trocar depos para Enum proprio
            venda_itens: array_map(
                fn(array $item) => VendaItemDTO::fromArray($item),
                $data['venda_itens'] ?? []
            ),
        );
    }

    // UPDATE: identidade é imutável — company_id, store_id e user_id ficam null
    // e somem no array_filter, então jamais são sobrescritos.
    public static function fromUpdateRequest(array $data): self
    {
        return new self(
            company_id:      null,
            store_id:        null,
            user_id:         null,
            market_place_id: $data['market_place_id'] ?? null,
            numero_pedido:   $data['numero_pedido'] ?? null,
            data_venda:      $data['data_venda'] ?? null,
            valor_bruto:      isset($data['valor_bruto']) ? (float) $data['valor_bruto'] : null,
            taxa_marketplace: isset($data['taxa_marketplace']) ? (float) $data['taxa_marketplace'] : null,
            valor_frete:      isset($data['valor_frete']) ? (float) $data['valor_frete'] : null,
            data_previsao_repasse: $data['data_previsao_repasse'] ?? null,
            observacao:            $data['observacao'] ?? null,
            status_id:             $data['status_id'] ?? Status::ACTIVE->value, // default ativo
        );
    }

    // Retorna apenas os campos presentes (não-null), evitando sobrescrever dados no update
    public function toArray(): array
    {
        return array_filter([
            'company_id'            => $this->company_id,
            'store_id'              => $this->store_id,
            'user_id'               => $this->user_id,
            'market_place_id'       => $this->market_place_id,
            'numero_pedido'         => $this->numero_pedido,
            'data_venda'            => $this->data_venda,
            'valor_bruto'           => $this->valor_bruto,
            'taxa_marketplace'      => $this->taxa_marketplace,
            'valor_frete'           => $this->valor_frete,
            'data_previsao_repasse' => $this->data_previsao_repasse,
            'observacao'            => $this->observacao,
            'status_id'             => $this->status_id,
        ], fn ($value) => !is_null($value));
    }
}
