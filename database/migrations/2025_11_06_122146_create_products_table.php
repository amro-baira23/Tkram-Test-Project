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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string("name");
            $table->string("slug")->unique();
            $table->string("description");
            $table->unsignedInteger("price");
            $table->unsignedInteger("quantity");
            $table->enum("status",["active","inactive"]);
            $table->timestamps();

            
            $table->index("slug");
            $table->index("status");
        });
    }
// slug, description, price, quantity, status, timestamps, 
// soft_deletes
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
