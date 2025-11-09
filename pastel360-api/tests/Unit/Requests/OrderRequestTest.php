<?php

namespace Tests\Unit\Requests;

use Tests\TestCase;
use App\Http\Requests\OrderRequest;
use Illuminate\Support\Facades\Validator;

class OrderRequestTest extends TestCase
{
    public function test_order_store_validation_passes_with_valid_data()
    {
        $data = [
            'product_id' => 1,
            'customer_id' => 1,
            'quantity' => 2,
            'unit_value' => 10.00,
            'status' => 'pending',
            'notes' => 'Entregar de manhã'
        ];

        $request = new OrderRequest();
        $request->setMethod('POST');

        $rules = $request->rules();

        $rules['product_id'] = 'required|integer';
        $rules['customer_id'] = 'required|integer';

        $validator = Validator::make($data, $rules);

        $this->assertFalse($validator->fails());
    }

    public function test_order_store_validation_fails_with_invalid_data()
    {
        $invalidData = [
            'product_id' => 'invalid',
            'customer_id' => 'invalid',
            'quantity' => 0,
            'unit_value' => -10,
            'status' => 'invalid_status',
            'notes' => 123
        ];

        $request = new OrderRequest();
        $request->setMethod('POST');

        $rules = $request->rules();
        $rules['product_id'] = 'required|integer';
        $rules['customer_id'] = 'required|integer';

        $validator = Validator::make($invalidData, $rules);

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('product_id', $validator->errors()->toArray());
        $this->assertArrayHasKey('customer_id', $validator->errors()->toArray());
        $this->assertArrayHasKey('quantity', $validator->errors()->toArray());
        $this->assertArrayHasKey('unit_value', $validator->errors()->toArray());
        $this->assertArrayHasKey('status', $validator->errors()->toArray());
    }

    public function test_order_update_validation_passes_with_valid_data()
    {
        $data = [
            'status' => 'approved',
            'notes' => 'Entregue com sucesso'
        ];

        $request = new OrderRequest();
        $request->setMethod('PUT');

        $rules = $request->rules();

        if (isset($rules['product_id'])) {
            $rules['product_id'] = 'sometimes|integer';
        }
        if (isset($rules['customer_id'])) {
            $rules['customer_id'] = 'sometimes|integer';
        }

        $validator = Validator::make($data, $rules);

        $this->assertFalse($validator->fails());
    }

    public function test_order_validation_respects_status_enum()
    {
        $invalidData = [
            'product_id' => 1,
            'customer_id' => 1,
            'quantity' => 2,
            'unit_value' => 10.00,
            'status' => 'invalid_status',
            'notes' => 'Teste'
        ];

        $request = new OrderRequest();
        $request->setMethod('POST');

        $rules = $request->rules();
        $rules['product_id'] = 'required|integer';
        $rules['customer_id'] = 'required|integer';

        $validator = Validator::make($invalidData, $rules);

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('status', $validator->errors()->toArray());
    }

    public function test_order_request_authorize_allows_access()
    {
        $request = new OrderRequest();

        $this->assertTrue($request->authorize());
    }
}
