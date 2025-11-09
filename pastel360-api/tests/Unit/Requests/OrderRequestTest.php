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
            'customer_id' => 1,
            'status' => 'pending',
            'notes' => 'Entregar de manhã',
            'items' => [
                [
                    'product_id' => 1,
                    'quantity' => 2,
                    'unit_value' => 8.50
                ]
            ]
        ];

        $request = new OrderRequest();
        $rules = $request->rules();

        // Para teste, substituir exists por integer
        $testRules = $this->adjustRulesForTest($rules, 'POST');

        $validator = Validator::make($data, $testRules);
        $this->assertFalse($validator->fails());
    }

    public function test_order_store_validation_fails_with_invalid_data()
    {
        $invalidData = [
            'customer_id' => 'invalid',
            'status' => 'invalid_status',
            'items' => [
                [
                    'product_id' => 'invalid',
                    'quantity' => 0,
                    'unit_value' => -10
                ]
            ]
        ];

        $request = new OrderRequest();
        $rules = $request->rules();
        $testRules = $this->adjustRulesForTest($rules, 'POST');

        $validator = Validator::make($invalidData, $testRules);

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('customer_id', $validator->errors()->toArray());
        $this->assertArrayHasKey('status', $validator->errors()->toArray());
        $this->assertArrayHasKey('items.0.product_id', $validator->errors()->toArray());
        $this->assertArrayHasKey('items.0.quantity', $validator->errors()->toArray());
        $this->assertArrayHasKey('items.0.unit_value', $validator->errors()->toArray());
    }

    public function test_order_store_validation_fails_without_items()
    {
        $invalidData = [
            'customer_id' => 1,
            'status' => 'pending',
            'items' => []
        ];

        $request = new OrderRequest();
        $rules = $request->rules();
        $testRules = $this->adjustRulesForTest($rules, 'POST');

        $validator = Validator::make($invalidData, $testRules);

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('items', $validator->errors()->toArray());
    }

    public function test_order_store_validation_fails_without_customer_id()
    {
        $invalidData = [
            'status' => 'pending',
            'items' => [
                [
                    'product_id' => 1,
                    'quantity' => 2,
                    'unit_value' => 8.50
                ]
            ]
        ];

        $request = new OrderRequest();
        $rules = $request->rules();
        $testRules = $this->adjustRulesForTest($rules, 'POST');

        $validator = Validator::make($invalidData, $testRules);

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('customer_id', $validator->errors()->toArray());
    }

    public function test_order_store_validation_fails_without_status()
    {
        $invalidData = [
            'customer_id' => 1,
            'items' => [
                [
                    'product_id' => 1,
                    'quantity' => 2,
                    'unit_value' => 8.50
                ]
            ]
        ];

        $request = new OrderRequest();
        $rules = $request->rules();
        $testRules = $this->adjustRulesForTest($rules, 'POST');

        $validator = Validator::make($invalidData, $testRules);

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('status', $validator->errors()->toArray());
    }

    public function test_order_store_validation_fails_with_missing_item_fields()
    {
        $invalidData = [
            'customer_id' => 1,
            'status' => 'pending',
            'items' => [
                [
                    // Faltando product_id, quantity, unit_value
                ]
            ]
        ];

        $request = new OrderRequest();
        $rules = $request->rules();
        $testRules = $this->adjustRulesForTest($rules, 'POST');

        $validator = Validator::make($invalidData, $testRules);

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('items.0.product_id', $validator->errors()->toArray());
        $this->assertArrayHasKey('items.0.quantity', $validator->errors()->toArray());
        $this->assertArrayHasKey('items.0.unit_value', $validator->errors()->toArray());
    }

    public function test_order_update_validation_passes_with_valid_data()
    {
        $data = [
            'status' => 'approved',
            'notes' => 'Entregue com sucesso'
        ];

        $request = new OrderRequest();
        $rules = $request->rules();
        $testRules = $this->adjustRulesForTest($rules, 'PUT');

        $validator = Validator::make($data, $testRules);
        $this->assertFalse($validator->fails());
    }

    public function test_order_update_validation_passes_with_items()
    {
        $data = [
            'status' => 'approved',
            'items' => [
                [
                    'id' => 1,
                    'product_id' => 1,
                    'quantity' => 3,
                    'unit_value' => 8.50
                ]
            ]
        ];

        $request = new OrderRequest();
        $rules = $request->rules();
        $testRules = $this->adjustRulesForTest($rules, 'PUT');

        $validator = Validator::make($data, $testRules);
        $this->assertFalse($validator->fails());
    }

    public function test_order_validation_respects_status_enum()
    {
        $validStatuses = ['pending', 'approved', 'delivered', 'canceled'];

        foreach ($validStatuses as $status) {
            $data = [
                'customer_id' => 1,
                'status' => $status,
                'items' => [
                    [
                        'product_id' => 1,
                        'quantity' => 2,
                        'unit_value' => 8.50
                    ]
                ]
            ];

            $request = new OrderRequest();
            $rules = $request->rules();
            $testRules = $this->adjustRulesForTest($rules, 'POST');

            $validator = Validator::make($data, $testRules);
            $this->assertFalse($validator->fails(), "Status '{$status}' deveria ser válido");
        }
    }

    public function test_order_validation_rejects_invalid_status()
    {
        $invalidData = [
            'customer_id' => 1,
            'status' => 'invalid_status',
            'items' => [
                [
                    'product_id' => 1,
                    'quantity' => 2,
                    'unit_value' => 8.50
                ]
            ]
        ];

        $request = new OrderRequest();
        $rules = $request->rules();
        $testRules = $this->adjustRulesForTest($rules, 'POST');

        $validator = Validator::make($invalidData, $testRules);
        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('status', $validator->errors()->toArray());
    }

    public function test_order_validation_for_items_structure()
    {
        $invalidData = [
            'customer_id' => 1,
            'status' => 'pending',
            'items' => 'not_an_array'
        ];

        $request = new OrderRequest();
        $rules = $request->rules();
        $testRules = $this->adjustRulesForTest($rules, 'POST');

        $validator = Validator::make($invalidData, $testRules);
        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('items', $validator->errors()->toArray());
    }

    public function test_order_request_authorize_allows_access()
    {
        $request = new OrderRequest();
        $this->assertTrue($request->authorize());
    }

    public function test_custom_validation_messages()
    {
        $invalidData = [
            // Dados inválidos para disparar todas as mensagens personalizadas
        ];

        $request = new OrderRequest();
        $messages = $request->messages();

        $this->assertArrayHasKey('customer_id.required', $messages);
        $this->assertArrayHasKey('customer_id.exists', $messages);
        $this->assertArrayHasKey('status.required', $messages);
        $this->assertArrayHasKey('status.in', $messages);
        $this->assertArrayHasKey('items.required', $messages);
        $this->assertArrayHasKey('items.array', $messages);
        $this->assertArrayHasKey('items.min', $messages);
        $this->assertArrayHasKey('items.*.product_id.required', $messages);
        $this->assertArrayHasKey('items.*.product_id.exists', $messages);
        $this->assertArrayHasKey('items.*.quantity.required', $messages);
        $this->assertArrayHasKey('items.*.quantity.min', $messages);
        $this->assertArrayHasKey('items.*.unit_value.required', $messages);
        $this->assertArrayHasKey('items.*.unit_value.min', $messages);
    }

    private function adjustRulesForTest(array $rules, string $method): array
    {
        $testRules = [];

        foreach ($rules as $key => $rule) {
            if (is_string($rule)) {
                $testRule = str_replace('exists:customers,id', 'integer', $rule);
                $testRule = str_replace('exists:products,id', 'integer', $testRule);

                if ($method === 'POST') {
                    $testRule = str_replace('sometimes|', 'required|', $testRule);
                }

                $testRules[$key] = $testRule;
            } else {
                $testRules[$key] = $rule;
            }
        }

        return $testRules;
    }

    public function test_order_update_validation_passes_with_partial_customer_id()
    {
        $data = [
            'customer_id' => 1,
            'status' => 'approved'
        ];

        $request = new OrderRequest();
        $rules = $request->rules();
        $testRules = $this->adjustRulesForTest($rules, 'PUT');

        $validator = Validator::make($data, $testRules);
        $this->assertFalse($validator->fails());
    }

    public function test_order_update_validation_passes_with_partial_status()
    {
        $data = [
            'status' => 'delivered'
        ];

        $request = new OrderRequest();
        $rules = $request->rules();
        $testRules = $this->adjustRulesForTest($rules, 'PUT');

        $validator = Validator::make($data, $testRules);
        $this->assertFalse($validator->fails());
    }

    public function test_order_update_validation_passes_with_notes_only()
    {
        $data = [
            'notes' => 'Atualizar observações'
        ];

        $request = new OrderRequest();
        $rules = $request->rules();
        $testRules = $this->adjustRulesForTest($rules, 'PUT');

        $validator = Validator::make($data, $testRules);
        $this->assertFalse($validator->fails());
    }

    public function test_order_update_validation_passes_with_partial_items()
    {
        $data = [
            'items' => [
                [
                    'product_id' => 1,
                    'quantity' => 3,
                    'unit_value' => 9.00
                ]
            ]
        ];

        $request = new OrderRequest();
        $rules = $request->rules();
        $testRules = $this->adjustRulesForTest($rules, 'PUT');

        $validator = Validator::make($data, $testRules);
        $this->assertFalse($validator->fails());
    }

    public function test_order_update_validation_passes_with_existing_item_id()
    {
        $data = [
            'items' => [
                [
                    'id' => 1,
                    'product_id' => 1,
                    'quantity' => 5,
                    'unit_value' => 8.50
                ]
            ]
        ];

        $request = new OrderRequest();
        $rules = $request->rules();
        $testRules = $this->adjustRulesForTest($rules, 'PUT');

        $validator = Validator::make($data, $testRules);
        $this->assertFalse($validator->fails());
    }

    public function test_order_update_validation_passes_with_new_item_without_id()
    {
        $data = [
            'items' => [
                [
                    'product_id' => 2,
                    'quantity' => 2,
                    'unit_value' => 7.50
                ]
            ]
        ];

        $request = new OrderRequest();
        $rules = $request->rules();
        $testRules = $this->adjustRulesForTest($rules, 'PUT');

        $validator = Validator::make($data, $testRules);
        $this->assertFalse($validator->fails());
    }

    public function test_order_update_validation_fails_with_invalid_items_structure()
    {
        $invalidData = [
            'items' => [
                [
                    'product_id' => 'invalid',
                    'quantity' => 0,
                    'unit_value' => -5.00
                ]
            ]
        ];

        $request = new OrderRequest();
        $rules = $request->rules();
        $testRules = $this->adjustRulesForTest($rules, 'PUT');

        $validator = Validator::make($invalidData, $testRules);
        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('items.0.product_id', $validator->errors()->toArray());
        $this->assertArrayHasKey('items.0.quantity', $validator->errors()->toArray());
        $this->assertArrayHasKey('items.0.unit_value', $validator->errors()->toArray());
    }

    public function test_order_post_validation_fails_without_customer_id()
    {
        $invalidData = [
            // customer_id faltando
            'status' => 'pending',
            'items' => [
                [
                    'product_id' => 1,
                    'quantity' => 2,
                    'unit_value' => 8.50
                ]
            ]
        ];

        $request = new OrderRequest();
        $request->setMethod('POST');

        $rules = $request->rules();
        $testRules = $this->adjustRulesForTest($rules, 'POST');

        $validator = Validator::make($invalidData, $testRules);

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('customer_id', $validator->errors()->toArray());
    }

    public function test_order_post_validation_fails_without_status()
    {
        $invalidData = [
            'customer_id' => 1,
            'items' => [
                [
                    'product_id' => 1,
                    'quantity' => 2,
                    'unit_value' => 8.50
                ]
            ]
        ];

        $request = new OrderRequest();
        $request->setMethod('POST');

        $rules = $request->rules();
        $testRules = $this->adjustRulesForTest($rules, 'POST');

        $validator = Validator::make($invalidData, $testRules);

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('status', $validator->errors()->toArray());
    }

    public function test_order_post_validation_fails_without_items()
    {
        $invalidData = [
            'customer_id' => 1,
            'status' => 'pending'
        ];

        $request = new OrderRequest();
        $request->setMethod('POST');

        $rules = $request->rules();
        $testRules = $this->adjustRulesForTest($rules, 'POST');

        $validator = Validator::make($invalidData, $testRules);

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('items', $validator->errors()->toArray());
    }

    public function test_order_post_validation_fails_with_empty_items_array()
    {
        $invalidData = [
            'customer_id' => 1,
            'status' => 'pending',
            'items' => []
        ];

        $request = new OrderRequest();
        $request->setMethod('POST');

        $rules = $request->rules();
        $testRules = $this->adjustRulesForTest($rules, 'POST');

        $validator = Validator::make($invalidData, $testRules);

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('items', $validator->errors()->toArray());
    }

    public function test_order_post_validation_fails_without_product_id_in_items()
    {
        $invalidData = [
            'customer_id' => 1,
            'status' => 'pending',
            'items' => [
                [
                    'quantity' => 2,
                    'unit_value' => 8.50
                ]
            ]
        ];

        $request = new OrderRequest();
        $request->setMethod('POST');

        $rules = $request->rules();
        $testRules = $this->adjustRulesForTest($rules, 'POST');

        $validator = Validator::make($invalidData, $testRules);

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('items.0.product_id', $validator->errors()->toArray());
    }

    public function test_order_post_validation_fails_without_quantity_in_items()
    {
        $invalidData = [
            'customer_id' => 1,
            'status' => 'pending',
            'items' => [
                [
                    'product_id' => 1,
                    'unit_value' => 8.50
                ]
            ]
        ];

        $request = new OrderRequest();
        $request->setMethod('POST');

        $rules = $request->rules();
        $testRules = $this->adjustRulesForTest($rules, 'POST');

        $validator = Validator::make($invalidData, $testRules);

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('items.0.quantity', $validator->errors()->toArray());
    }

    public function test_order_post_validation_fails_without_unit_value_in_items()
    {
        $invalidData = [
            'customer_id' => 1,
            'status' => 'pending',
            'items' => [
                [
                    'product_id' => 1,
                    'quantity' => 2
                ]
            ]
        ];

        $request = new OrderRequest();
        $request->setMethod('POST');

        $rules = $request->rules();
        $testRules = $this->adjustRulesForTest($rules, 'POST');

        $validator = Validator::make($invalidData, $testRules);

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('items.0.unit_value', $validator->errors()->toArray());
    }

    public function test_order_post_validation_fails_with_invalid_quantity()
    {
        $invalidData = [
            'customer_id' => 1,
            'status' => 'pending',
            'items' => [
                [
                    'product_id' => 1,
                    'quantity' => 0,
                    'unit_value' => 8.50
                ]
            ]
        ];

        $request = new OrderRequest();
        $request->setMethod('POST');

        $rules = $request->rules();
        $testRules = $this->adjustRulesForTest($rules, 'POST');

        $validator = Validator::make($invalidData, $testRules);

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('items.0.quantity', $validator->errors()->toArray());
    }

    public function test_order_post_validation_fails_with_invalid_unit_value()
    {
        $invalidData = [
            'customer_id' => 1,
            'status' => 'pending',
            'items' => [
                [
                    'product_id' => 1,
                    'quantity' => 2,
                    'unit_value' => 0.00
                ]
            ]
        ];

        $request = new OrderRequest();
        $request->setMethod('POST');

        $rules = $request->rules();
        $testRules = $this->adjustRulesForTest($rules, 'POST');

        $validator = Validator::make($invalidData, $testRules);

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('items.0.unit_value', $validator->errors()->toArray());
    }
}
