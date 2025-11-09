<?php

namespace Tests\Unit\Requests;

use App\Http\Requests\CustomerRequest;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\DatabasePresenceVerifier;
use Mockery;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CustomerRequestTest extends TestCase
{
    use RefreshDatabase;
    private function getValidator(array $data)
    {
        $request = new CustomerRequest();
        $request->merge($data);

        return Validator::make($data, $request->rules());
    }

    public function test_customer_store_validation()
    {
        $data = [
            'name' => 'José',
            'mail' => 'jromano@test.com',
            'phone' => '123456',
            'birthdate' => '1996-01-01',
            'place' => 'Main St',
            'number' => '10',
            'zipcode' => '12345678',
            'district' => 'Centro'
        ];

        $request = new CustomerRequest();
        $request->setMethod('POST');

        $rules = $request->rules();

        $validator = Validator::make($data, $rules);

        $this->assertFalse($validator->fails());
    }

    public function test_customer_update_validation()
    {
        $data = [
            'name' => 'José Guilherme',
            'mail' => 'jromano@test.com',
            'phone' => '123456',
            'birthdate' => '1996-01-01',
            'place' => 'Main St',
            'number' => '10',
            'zipcode' => '12345678',
            'district' => 'Centro'
        ];

        $request = new CustomerRequest();
        $request->setMethod('PUT');

        $rules = $request->rules();

        $validator = Validator::make($data, $rules);

        $this->assertFalse($validator->fails());
    }

    public function test_customer_store_validation_fails_if_mail_exists()
    {
        $verifier = Mockery::mock(DatabasePresenceVerifier::class);
        $verifier->shouldReceive('getCount')->andReturn(1);
        $verifier->shouldReceive('setConnection')->andReturnSelf();

        Validator::setPresenceVerifier($verifier);

        $validator = $this->getValidator([
            'name' => 'José',
            'mail' => 'jromano@test.com',
            'phone' => '123456',
            'birthdate' => '1996-01-01',
            'place' => 'Main St',
            'number' => '10',
            'zipcode' => '12345678',
            'district' => 'Centro'
        ]);

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('mail', $validator->errors()->toArray());
    }

    public function test_customer_request_authorize_allows_access()
    {
        $request = new CustomerRequest();

        $this->assertTrue($request->authorize());
    }
}
