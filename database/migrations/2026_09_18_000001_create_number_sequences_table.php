<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('number_sequences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained('branches')->cascadeOnDelete();
            $table->string('document_type', 20); // RES, FOL, INV, ORD, EXP
            $table->unsignedBigInteger('current_value')->default(0);
            $table->timestamps();

$table->unique(['branch_id', 'document_type']);
        });
    }

public function down(): void
    {
        Schema::dropIfExists('number_sequences');
    }
};
