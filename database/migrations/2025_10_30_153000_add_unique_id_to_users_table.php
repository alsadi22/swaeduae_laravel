<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('unique_id')->nullable()->unique()->after('id');
        });
        
        // Populate existing users with unique IDs
        // Using a database-agnostic approach for padding
        DB::table('users')->whereNull('unique_id')->orderBy('id')->eachById(function ($user) {
            $paddedId = str_pad($user->id, 6, '0', STR_PAD_LEFT);
            DB::table('users')->where('id', $user->id)->update([
                'unique_id' => 'SV' . $paddedId
            ]);
        });
        
        // Make the column non-nullable after populating
        Schema::table('users', function (Blueprint $table) {
            $table->string('unique_id')->nullable(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('unique_id');
        });
    }
};