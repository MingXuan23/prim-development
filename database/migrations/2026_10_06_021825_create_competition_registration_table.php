<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCompetitionRegistrationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('competition_registration', function (Blueprint $table) {
            $table->id('id');
            $table->foreignId('competitionId')->constrained('competitions', 'id')->onDelete('cascade');
            $table->foreignId('teamId')->constrained('teams', 'id')->onDelete('cascade');

            $table->enum('statusPayment', ['Paid', 'Free', 'Pending'])->default('Pending');
            $table->timestamp('registeredDate')->useCurrent()->useCurrentOnUpdate();
            $table->string('winnerPlacement')->nullable();
            $table->string('certificateSelected')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('competition_registration');
    }
}
