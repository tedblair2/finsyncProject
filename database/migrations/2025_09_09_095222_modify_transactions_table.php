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
        //
        Schema::table('transactions', function (Blueprint $table) {
            $table->string('ftCr_narration')->nullable()->default(null)->change();
            $table->string('phone_number')->nullable()->default(null)->change();
            $table->string('narrative')->nullable()->default(null)->change();
            $table->enum('creditdebitflag',['credit','debit'])->default('credit');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
        Schema::table('transactions', function (Blueprint $table) {
            $table->string('narrative')->default('')->change();
            $table->string('ftCr_narration')->default('')->change();
            $table->string('phone_number')->default('')->change();
            $table->dropColumn('creditdebitflag');
        });
    }
};
