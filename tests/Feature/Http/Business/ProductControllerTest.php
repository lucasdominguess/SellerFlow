<?php

namespace Tests\Feature\Http\Business;

use App\Models\Business\Product;
use App\Models\Business\ProductImage;
use App\Models\Business\Supplier;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

describe('ProductController', function () {

    beforeEach(function () {
        DB::table('status')->insert([
            ['id' => 1, 'name' => 'Ativo'],
            ['id' => 2, 'name' => 'Inativo'],
        ]);

        // produtos não têm company_id (catálogo global): basta um usuário autenticado
        actingAsJwt();
        $this->withHeader('Accept', 'application/json'); // garante JSON também em post() multipart

        $this->payload = fn (array $override = []) => array_merge([
            'sku'        => 'CAPA',
            'name'       => 'Capa de Celular',
            'price_unit' => 10.00,
            'price_box'  => 90.00,
            'status_id'  => 1,
        ], $override);
    });

    it('creates a product (without images)', function () {
        $response = $this->postJson('/api/v1/product', ($this->payload)());

        $response->assertStatus(201)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.name', 'Capa de Celular');

        $this->assertDatabaseHas('products', ['name' => 'Capa de Celular']);
    });

    it('creates a product with uploaded images', function () {
        Storage::fake('public');

        $response = $this->post('/api/v1/product', ($this->payload)([
            'images' => [
                UploadedFile::fake()->image('a.jpg'),
                UploadedFile::fake()->image('b.jpg'),
            ],
        ]));

        $response->assertStatus(201);

        $productId = $response->json('data.id');
        expect($response->json('data.images'))->toHaveCount(2);
        $this->assertDatabaseCount('product_images', 2);

        foreach (ProductImage::where('product_id', $productId)->pluck('path') as $path) {
            Storage::disk('public')->assertExists($path);
        }
    });

    it('validates required fields with 422', function () {
        $this->postJson('/api/v1/product', ($this->payload)(['name' => null]))
            ->assertStatus(422);
    });

    it('shows a product', function () {
        $supplier = Supplier::factory()->create();
        $product  = Product::factory()->create(['status_id' => 1, 'fornecedor_id' => $supplier->id]);

        $this->getJson("/api/v1/product/{$product->id}")
            ->assertStatus(200)
            ->assertJsonPath('data.id', $product->id);
    });

    it('updates a product name', function () {
        $supplier = Supplier::factory()->create();
        $product  = Product::factory()->create(['name' => 'Nome Antigo', 'status_id' => 1, 'fornecedor_id' => $supplier->id]);

        $this->putJson("/api/v1/product/{$product->id}", ['name' => 'Nome Novo'])
            ->assertStatus(200)
            ->assertJsonPath('data.name', 'Nome Novo');

        $this->assertDatabaseHas('products', ['id' => $product->id, 'name' => 'Nome Novo']);
    });

    it('deletes a product and removes its image files', function () {
        Storage::fake('public');

        $productId = $this->post('/api/v1/product', ($this->payload)([
            'images' => [UploadedFile::fake()->image('a.jpg')],
        ]))->json('data.id');

        $path = ProductImage::where('product_id', $productId)->value('path');
        Storage::disk('public')->assertExists($path);

        $this->deleteJson("/api/v1/product/{$productId}")->assertStatus(200);

        $this->assertDatabaseMissing('products', ['id' => $productId]);
        $this->assertDatabaseCount('product_images', 0);
        Storage::disk('public')->assertMissing($path);
    });

    it('lists products paginated', function () {
        $supplier = Supplier::factory()->create();
        Product::factory()->count(2)->create(['status_id' => 1, 'fornecedor_id' => $supplier->id]);

        $this->getJson('/api/v1/product')
            ->assertStatus(200)
            ->assertJsonStructure(['data', 'meta' => ['total', 'per_page', 'current_page', 'last_page']]);
    });
});
