<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('floors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained('branches')->cascadeOnDelete();
            $table->integer('floor_number');
            $table->string('name');
            $table->string('description')->nullable();
            $table->timestamps();
        });

        Schema::create('room_types', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained('branches')->cascadeOnDelete();
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('base_rate', 12, 2);
            $table->integer('max_adults')->default(2);
            $table->integer('max_children')->default(1);
            $table->string('bed_type')->default('King');
            $table->integer('bed_count')->default(1);
            $table->string('status')->default('active');
            $table->timestamps();
        });

        Schema::create('rooms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained('branches')->cascadeOnDelete();
            $table->foreignId('floor_id')->constrained('floors')->cascadeOnDelete();
            $table->foreignId('room_type_id')->constrained('room_types')->cascadeOnDelete();
            $table->string('room_number');
            $table->text('description')->nullable();
            $table->string('operational_status')->default('AVAILABLE'); // AVAILABLE, RESERVED, OCCUPIED, OUT_OF_ORDER, MAINTENANCE
            $table->string('housekeeping_status')->default('CLEAN');   // DIRTY, CLEANING, CLEAN, INSPECTED
            $table->string('maintenance_status')->default('NONE');    // NONE, REPORTED, IN_MAINTENANCE
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['branch_id', 'room_number']);
        });

        Schema::create('amenities', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('room_type_amenity', function (Blueprint $table) {
            $table->foreignId('room_type_id')->constrained('room_types')->cascadeOnDelete();
            $table->foreignId('amenity_id')->constrained('amenities')->cascadeOnDelete();
            $table->primary(['room_type_id', 'amenity_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('room_type_amenity');
        Schema::dropIfExists('amenities');
        Schema::dropIfExists('rooms');
        Schema::dropIfExists('room_types');
        Schema::dropIfExists('floors');
    }
};
