<?php

namespace Tests\Feature;

use App\Http\Services\ProductService;
use Tests\TestCase;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Mockery;

class ProductControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_store_a_product_successfully()
    {
        // Arrange
        $user = User::factory()->create();
        $this->actingAs($user, 'api');

        $this->mock(ProductService::class, function ($mock) {
            $mock->shouldReceive('store')
                ->once()
                ->andReturn(Product::factory()->make([
                    'name' => 'Test Product',
                    'price' => 100,
                    'stock_quantity' => 10,
                ]));
        });

        $payload = [
            'name' => 'Test Product',
            'price' => 100,
            'stock_quantity' => 10,
        ];

        // Act
        $response = $this->postJson(url('api/products'), $payload);

        // Assert
        $response->assertStatus(Response::HTTP_CREATED)
            ->assertJson([
                'message' => 'Product created successfully.',
                'data' => [
                    'name' => 'Test Product',
                    'price' => 100,
                    'stock_quantity' => 10,
                ],
            ]);
    }

    /** @test */
    public function it_returns_error_if_store_fails()
    {
        // Arrange
        $user = User::factory()->create();
        $this->actingAs($user, 'api');

        $this->mock(ProductService::class, function ($mock) {
            $mock->shouldReceive('store')
                ->once()
                ->andThrow(new \Exception('Failed to create product'));
        });

        $payload = [
            'name' => 'Test Product',
            'price' => 100,
            'stock_quantity' => 10,
        ];

        // Act
        $response = $this->postJson(url('api/products'), $payload);

        // Assert
        $response->assertStatus(Response::HTTP_BAD_REQUEST)
            ->assertJson([
                'message' => 'Failed to create product',
            ]);
    }

    /** @test */
    public function it_requires_authentication_to_store_a_product()
    {
        // Arrange
        $payload = [
            'name' => 'Test Product',
            'price' => 100,
            'description' => 'This is a test product.',
        ];

        // Act
        $response = $this->postJson(url('api/products'), $payload);

        // Assert
        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function it_can_update_a_product_successfully()
    {
        // Arrange
        $user = User::factory()->create();
        $this->actingAs($user, 'api');

        $product = Product::factory()->create([
            'id' => 1,
            'name' => 'Old Product Name',
            'price' => 50,
            'stock_quantity' => 5,
        ]);

        $this->mock(ProductService::class, function ($mock) use ($product) {
            $mock->shouldReceive('update')
                ->once()
                ->withArgs(function ($data, $prod) use ($product) {
                    return $data['name'] === 'Updated Product Name' &&
                        $data['price'] === 100 &&
                        $data['stock_quantity'] === 10 &&
                        $prod->is($product);
                })
                ->andReturn(true);
        });

        $payload = [
            'name' => 'Updated Product Name',
            'price' => 100,
            'stock_quantity' => 10,
        ];

        // Act
        $response = $this->putJson(url("api/products/{$product->id}"), $payload);

        // Assert
        $response->assertStatus(Response::HTTP_OK)
            ->assertJson([
                'message' => 'Product updated successfully.',
                'data' => [
                    'id' => $product->id,
                    'name' => 'Updated Product Name',
                    'price' => 100,
                    'stock_quantity' => 10,
                ],
            ]);
    }

}

