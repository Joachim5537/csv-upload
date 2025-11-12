<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product', function (Blueprint $table) {
            $table->string('UNIQUE_KEY')->primary();
            $table->string('PRODUCT_TITLE',255)->nullable();
            $table->text('PRODUCT_DESCRIPTION')->nullable();
            $table->string('STYLE#')->nullable();
            $table->string('SANMAR_MAINFRAME_COLOR')->nullable();
            $table->string('SIZE')->nullable();
            $table->string('COLOR_NAME')->nullable();
            $table->decimal('PIECE_PRICE', 10, 2)->nullable();


            $table->timestamps();
        });

    }

    public function down(): void
    {
        Schema::dropIfExists('product');
    }
};
