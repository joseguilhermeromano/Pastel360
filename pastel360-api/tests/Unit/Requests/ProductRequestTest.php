<?php

namespace Tests\Unit\Requests;

use App\Http\Requests\ProductRequest;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class ProductRequestTest extends TestCase
{

    public function test_product_store_validation()
    {
        $data = [
            'name' => 'PASTEL CAIPIRA',
            'description' => 'FRANGO, CATUPIRY E MILHO',
            'price' => 10.00,
            'photo' => 'pastel-caipira.jpg',
            'stock' => 20,
            'enable' => true,
        ];

        $request = new ProductRequest();
        $request->setMethod('POST');

        $rules = $request->rules();

        $validator = Validator::make($data, $rules);

        $this->assertFalse($validator->fails());
    }

    public function test_product_update_validation()
    {
        $data = [
            'name' => 'PASTEL CAIPIRA',
            'description' => 'FRANGO, CATUPIRY E MILHO',
            'price' => 10.00,
            'photo' => 'pastel-caipira.jpg',
            'stock' => 20,
            'enable' => true,
        ];

        $request = new ProductRequest();
        $request->setMethod('PUT');

        $rules = $request->rules();

        $validator = Validator::make($data, $rules);

        $this->assertFalse($validator->fails());
    }

    public function test_product_request_authorize_allows_access()
    {
        $request = new ProductRequest();

        $this->assertTrue($request->authorize());
    }
}
