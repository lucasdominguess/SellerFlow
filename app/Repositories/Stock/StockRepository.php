<?php

namespace App\Repositories\Stock;

use App\Contracts\Repositories\Stock\StockRepositoryInterface;
use App\Models\Stock\Stock;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class StockRepository implements StockRepositoryInterface
{
    public function __construct(
        private Stock $stockModel,
    ) {}

    public function index(int $perPage = 15, int $page = 1, ?array $filters = []): LengthAwarePaginator
    {
        $query = $this->stockModel->query();

        if (empty($filters)) return $query->orderByDesc('id')->paginate($perPage);

        // Adicione filtros específicos aqui:
        // if (!empty($filters['name'])) {
        //     $query->where('name', 'like', '%' . $filters['name'] . '%');
        // }

        return $query->orderByDesc('id')->paginate($perPage);
    }

    public function show(Stock $stock): Stock
    {
        // Route model binding já buscou o registro — adicione $->load('relacao') se precisar
        return $stock;
    }

    public function store(array $data): Stock
    {
        return $this->stockModel->create($data);
    }

    public function update(Stock $stock, array $data): Stock
    {
        $stock->update($data);

        return $stock;
    }

    public function delete(Stock $stock)
    {
        return $stock->delete();
    }

    public function checkQuantityProductsInStock(int $companyId, ?int $productId = null, ?string $productName = null, ?string $sku = null): Collection
    {
        return DB::table('movimentacoes_estoque as me')
            ->join('products as p', 'p.id', '=', 'me.product_id')
            ->join('companies as c', 'c.id', '=', 'me.company_id')
            ->leftJoin('ajustes_estoque as ae', function ($join) {
                $join->on('ae.id', '=', 'me.origem_id')
                    ->where('me.origem_tipo', '=', 'ajuste_manual')
                    ->where('me.tipo', '=', 'ajuste');
            })
            ->where('me.company_id', $companyId)
            ->when($productId, fn ($query) => $query->where('me.product_id', $productId))
            ->when($productName, fn ($query) => $query->where('p.name', 'ilike', "%{$productName}%"))
            ->when($sku, fn ($query) => $query->where('p.sku', 'ilike', "%{$sku}%"))
            ->groupBy('me.company_id', 'c.name', 'me.product_id', 'p.sku', 'p.name')
            ->selectRaw('me.company_id, c.name as company_name, me.product_id, p.sku, p.name as product_name')
            ->selectRaw(<<<'SQL'
                (
                    select u.name
                    from ajustes_estoque ae2
                    inner join users u on u.id = ae2.user_id
                    where ae2.product_id = me.product_id
                      and ae2.company_id = me.company_id
                    order by ae2.created_at desc, ae2.id desc
                    limit 1
                ) as last_adjustment_user
            SQL)
            ->selectRaw("coalesce(sum(me.quantidade) filter (where me.tipo = 'entrada'), 0) as total_entradas")
            ->selectRaw("coalesce(sum(me.quantidade) filter (where me.tipo = 'saida'), 0) as total_saidas")
            ->selectRaw("coalesce(sum(me.quantidade) filter (where me.tipo = 'ajuste' and ae.quantidade > 0), 0) as total_ajustes_positivos")
            ->selectRaw("coalesce(sum(me.quantidade) filter (where me.tipo = 'ajuste' and ae.quantidade < 0), 0) as total_ajustes_negativos")
            ->selectRaw(<<<'SQL'
                coalesce(sum(me.quantidade) filter (where me.tipo = 'entrada'), 0)
                - coalesce(sum(me.quantidade) filter (where me.tipo = 'saida'), 0)
                + coalesce(sum(me.quantidade) filter (where me.tipo = 'ajuste' and ae.quantidade > 0), 0)
                - coalesce(sum(me.quantidade) filter (where me.tipo = 'ajuste' and ae.quantidade < 0), 0)
                as saldo_atual
            SQL)
            ->orderBy('p.name')
            ->get();
    }
}
