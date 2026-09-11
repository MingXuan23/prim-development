<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateParticipantsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('participants', function (Blueprint $table) {
            $table->id('id');
            $table->string('name');
            $table->string('icNo', 25)->comment('Simpan No IC / No Pasport');
            $table->string('email');
            $table->string('noTel', 20);
            $table->enum('gender', ['Lelaki', 'Perempuan']);
            $table->string('currentGrade', 100);
            $table->string('supervisorName', 100);
            $table->string('supervisorEmail', 255);
            $table->string('supervisorNoTel', 20);
            $table->enum('isLeader', ['Leader', 'Member'])->nullable();
            $table->foreignId('teamId')->constrained('teams', 'id')->onDelete('cascade');
            $table->foreignId('userId')->constrained('users', 'id')->onDelete('cascade');
            $table->foreignId('institutionId')->constrained('institutions', 'id')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('participants');
    }
}
