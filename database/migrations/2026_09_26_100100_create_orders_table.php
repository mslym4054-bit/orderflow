<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('customer_id')->constrained()->cascadeOnDelete();
            $table->string('item');
            $table->unsignedInteger('quantity')->default(1);
            $table->decimal('price', 10, 2)->nullable();
            $table->enum('status', ['جديد', 'قيد التجهيز', 'تم الشحن', 'تم التسليم', 'ملغي'])
                ->default('جديد');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
