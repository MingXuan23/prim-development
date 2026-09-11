<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCompetitionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
       Schema::create('competitions', function (Blueprint $table) {
            $table->id('id');
            $table->string('competitionTitle', 100)->nullable();
            $table->string('category', 100)->nullable();
            $table->string('venue', 255)->nullable();
            $table->timestamp('registerOpen')->nullable();
            $table->timestamp('registerClose')->nullable();
            $table->timestamp('competitionStart')->nullable();
            $table->timestamp('competitionEnd')->nullable();
            $table->integer('minimumAge')->nullable();
            $table->integer('maximumAge')->nullable();
            $table->decimal('nationalFees', 10, 2)->nullable()->default(0.00);
            $table->decimal('internationalFees', 10, 2)->nullable()->default(0.00);
            $table->enum('participateType', ['Individual', 'Team'])->nullable();
            $table->integer('minimumParticipate')->nullable();
            $table->integer('maximumParticipate')->nullable();
            $table->longText('description')->nullable();
            $table->string('imagePoster', 255)->nullable();
            $table->enum('status', ['Published', 'Draft']);
            $table->foreignId('userId')->constrained('users', 'id')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('competitions');
    }
}
