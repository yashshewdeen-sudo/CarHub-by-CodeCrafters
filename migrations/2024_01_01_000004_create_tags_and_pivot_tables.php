<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('tags', function (Blueprint $t) {
            $t->id();
            $t->string('name', 60);
            $t->string('slug', 60)->unique();
            $t->timestamps();
        });

        Schema::create('listing_tag', function (Blueprint $t) {
            $t->foreignId('listing_id')->constrained('listings')->cascadeOnDelete();
            $t->foreignId('tag_id')->constrained('tags')->cascadeOnDelete();
            $t->primary(['listing_id', 'tag_id']);
        });
    }
    public function down(): void {
        Schema::dropIfExists('listing_tag');
        Schema::dropIfExists('tags');
    }
};
