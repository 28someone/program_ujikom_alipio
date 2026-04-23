<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('class_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('slug')->unique();
            $table->timestamps();
        });

        $now = now();
        $defaultCategories = collect([
            'X PPLG 1',
            'X PPLG 2',
            'X PPLG 3',
            'XI PPLG 1',
            'XI PPLG 2',
            'XI PPLG 3',
            'XII PPLG 1',
            'XII PPLG 2',
            'XII PPLG 3',
        ])->map(fn (string $name) => [
            'name' => $name,
            'slug' => Str::slug($name),
            'created_at' => $now,
            'updated_at' => $now,
        ])->all();

        DB::table('class_categories')->insert($defaultCategories);

        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('class_category_id')->nullable()->after('class_name')->constrained('class_categories')->nullOnDelete();
        });

        $categoryMap = DB::table('class_categories')->pluck('id', 'name');

        DB::table('users')
            ->select('id', 'class_name')
            ->whereNotNull('class_name')
            ->orderBy('id')
            ->get()
            ->each(function (object $user) use ($categoryMap) {
                $categoryId = $categoryMap[$user->class_name] ?? null;

                if ($categoryId) {
                    DB::table('users')
                        ->where('id', $user->id)
                        ->update(['class_category_id' => $categoryId]);
                }
            });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('class_category_id');
        });

        Schema::dropIfExists('class_categories');
    }
};
