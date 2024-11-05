<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Position;


return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('name', 60)->change();
            $table->string('password')->nullable()->change();
            $table->foreignIdFor(Position::class)
                ->after('email')
                ->references(app(Position::class)->getKeyName())
                ->on(app(Position::class)->getTable());
            $table->string('phone', 20)->after('email');
            $table->string('photo_file_path')->nullable()->after('phone');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('name')->change();
            $table->dropForeignIdFor(Position::class);
            $table->dropColumn('phone');
            $table->dropColumn('photo_file_path');
        });
    }
};
