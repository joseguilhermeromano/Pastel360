<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('mail')->unique();
            $table->string('phone')->nullable();
            $table->date('birthdate')->nullable();
            $table->string('place')->nullable();
            $table->string('number')->nullable();
            $table->string('zipcode')->nullable();
            $table->string('district')->nullable();
            $table->string('complement')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};
