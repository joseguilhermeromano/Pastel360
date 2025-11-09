<?php

namespace Database\Factories;

use App\Models\CustomerModel;
use Illuminate\Database\Eloquent\Factories\Factory;

class CustomerModelFactory extends Factory
{
    protected $model = CustomerModel::class;

    public function definition()
    {
        return [
            'name' => $this->faker->name,
            'mail' => $this->faker->unique()->email,
            'phone' => $this->faker->phoneNumber,
            'birthdate' => $this->faker->date(),
            'place' => $this->faker->streetName,
            'number' => $this->faker->buildingNumber,
            'zipcode' => $this->faker->postcode,
            'district' => $this->faker->citySuffix,
            'complement' => $this->faker->secondaryAddress,
        ];
    }
}
