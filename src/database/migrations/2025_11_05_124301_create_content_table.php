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
        Schema::create('content', function (Blueprint $table) {
            $table->id();

            // morph relation (also to ulid)
            $table->string('contentable_type');
            $table->string('contentable_id');

            // field key on the parent model, ex: "header", "body", "footer"
            $table->string('key');

            // content block type, ex: "Text", "HeadlineText", "Image", etc.
            $table->string('type');

            // content should be json to allow flexible content structures
            $table->json('content');

            // order column to order multiple contents for one contentable
            $table->integer('order')->default(0);

            // indexes for faster lookups
            $table->index(['contentable_type', 'contentable_id', 'key']);
            $table->index(['key', 'type']);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('content');
    }
};
