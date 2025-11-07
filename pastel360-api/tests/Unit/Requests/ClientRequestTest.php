<?php

namespace Tests\Unit\Requests;

use App\Http\Requests\ClientRequest;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\DatabasePresenceVerifier;
use Mockery;
use Tests\TestCase;

class ClientRequestTest extends TestCase
{
    private function getValidator(array $data)
    {
        $request = new ClientRequest();
        $request->merge($data);

        return Validator::make($data, $request->rules());
    }

    public function test_client_store_validation()
    {
        $verifier = Mockery::mock(DatabasePresenceVerifier::class);
        $verifier->shouldReceive('getCount')->andReturn(0);
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

        $this->assertTrue($validator->passes());
    }

    public function test_client_store_validation_fails_if_mail_exists()
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

    public function test_client_request_authorize_allows_access()
    {
        $request = new ClientRequest();

        $this->assertTrue($request->authorize());
    }
}
