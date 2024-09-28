<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCodeRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('code_requests', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email');
            $table->string('phone');
            $table->unsignedBigInteger('language_id')->nullable();
            $table->unsignedBigInteger('package_id')->nullable();
            $table->unsignedBigInteger('transaction_id')->nullable();
            $table->decimal('final_price', 8, 2);
            $table->string('status')->default('pending');

            $table->text('source_code')->nullable();
            $table->text('problem_description')->nullabel();
           
            $table->boolean('email_sent')->default(false);
            $table->json('helpers')->nullable(); 

            $table->foreign('language_id')->references('id')->on('code_language')->onDelete('set null');
            $table->foreign('package_id')->references('id')->on('code_package')->onDelete('set null');
            $table->foreign('transaction_id')->references('id')->on('transactions')->onDelete('cascade');

          

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
        Schema::dropIfExists('code_requests');
    }
}
