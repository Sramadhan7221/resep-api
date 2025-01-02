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
        Schema::create('tbl_resep', function (Blueprint $table) {
            $table->id();
            $table->string('nama_resep');
            $table->text('desc_resep')->nullable();
            $table->text('langkah');
            $table->timestamps();
        });

        Schema::create('tbl_bahan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('resep_id');
            $table->text('desc_bahan');
            $table->timestamps();

            $table->foreign('resep_id', 'resep_bahan_FK')
                ->references('id')
                ->on('tbl_resep')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_resep');
        Schema::dropIfExists('tbl_bahan');
    }
};
