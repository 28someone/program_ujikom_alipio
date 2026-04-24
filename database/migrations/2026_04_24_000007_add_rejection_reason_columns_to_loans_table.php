<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('loans', function (Blueprint $table) {
            $table->text('rejection_reason')->nullable()->after('note');
            $table->text('return_rejection_reason')->nullable()->after('return_note');
        });
    }

    public function down(): void
    {
        Schema::table('loans', function (Blueprint $table) {
            $table->dropColumn(['rejection_reason', 'return_rejection_reason']);
        });
    }
};
