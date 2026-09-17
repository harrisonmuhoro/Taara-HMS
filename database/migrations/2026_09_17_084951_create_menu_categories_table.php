<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Menu categories are created by the consolidated restaurant migration.
    }

    public function down(): void
    {
        // Menu categories are owned by the consolidated restaurant migration.
    }
};
