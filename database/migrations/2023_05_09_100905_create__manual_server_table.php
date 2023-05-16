<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('Test_Results', function (Blueprint $table) {
            $table->id();
            $table->string('Testas');
            $table->string('Rezultatas');
            $table->float('Laikas');
            $table->json('Testo_parametrai');
            $table->json('Sukurtas_serveris')->nullable();
            $table->string('Zinute');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('Test_Results');
    }
};
