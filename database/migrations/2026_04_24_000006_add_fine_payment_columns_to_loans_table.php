<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('loans', function (Blueprint $table) {
            $table->timestamp('fine_paid_at')->nullable()->after('fine_amount');
            $table->foreignId('fine_paid_by')->nullable()->after('fine_paid_at')->constrained('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('loans', function (Blueprint $table) {
            $table->dropConstrainedForeignId('fine_paid_by');
            $table->dropColumn('fine_paid_at');
        });
    }
};
