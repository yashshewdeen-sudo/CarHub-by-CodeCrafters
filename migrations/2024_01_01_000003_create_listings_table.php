<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('listings', function (Blueprint $t) {
            $t->id();
            $t->foreignId('seller_id')->constrained('users')->cascadeOnDelete();
            $t->foreignId('category_id')->nullable()->constrained('categories')->nullOnDelete();
            $t->string('make', 50);
            $t->string('model', 50);
            $t->smallInteger('year');
            $t->integer('mileage')->default(0);
            $t->decimal('price', 12, 2);
            $t->enum('fuel_type', ['Petrol', 'Diesel', 'Hybrid', 'Electric']);
            $t->enum('transmission', ['Manual', 'Automatic', 'CVT']);
            $t->enum('condition_status', ['New', 'Used'])->default('Used');
            $t->text('description')->nullable();
            $t->enum('status', ['Pending', 'Active', 'Sold', 'Rejected'])->default('Pending');
            $t->timestamps();

            $t->index(['status']);
            $t->index(['make', 'model']);
        });
    }
    public function down(): void {
        Schema::dropIfExists('listings');
    }
};
