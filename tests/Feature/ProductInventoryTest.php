<?php
namespace Tests\Feature;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
class ProductInventoryTest extends TestCase
{
    protected function setUp(): void { parent::setUp(); config(['app.storage' => storage_path('framework/testing')]); @mkdir(storage_path('app/private'),0775,true); @unlink(storage_path('app/private/products.json')); @unlink(storage_path('app/private/products.json.lock')); }
    public function test_products_are_saved_rendered_and_editable(): void
    {
        $this->postJson('/products',['name'=>'Widget','quantity'=>'3','price'=>'2.50'])->assertCreated()->assertJsonPath('product.name','Widget');
        $this->assertFileExists(storage_path('app/private/products.json'));
        $id=json_decode(file_get_contents(storage_path('app/private/products.json')),true)[0]['id'];
        $this->putJson('/products/'.$id,['name'=>'New Widget','quantity'=>'4','price'=>'3.25'])->assertOk()->assertJsonPath('product.name','New Widget');
        $this->get('/')->assertOk()->assertSee('Product name')->assertSee('Grand total')->assertSee('New Widget');
    }
    public function test_invalid_values_are_rejected_and_empty_list_renders(): void
    {
        $this->postJson('/products',['name'=>'','quantity'=>'-1','price'=>'1.234'])->assertUnprocessable()->assertJsonValidationErrors(['name','quantity','price']);
        $this->get('/')->assertOk()->assertSee('No products have been submitted yet.');
    }
}
