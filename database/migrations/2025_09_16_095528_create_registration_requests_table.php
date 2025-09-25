<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRegistrationRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('registration_requests', function (Blueprint $table) {
            $table->id();
            $table->json('student_info');
            $table->string('status');

            $table->unsignedBigInteger('organization_id')->index();
            $table->foreign("organization_id")->references("id")->on("organizations")->onDelete('cascade');

            $table->unsignedBigInteger('parent_id')->index();
            $table->foreign("parent_id")->references("id")->on("users")->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('registration_requests');
    }
}
