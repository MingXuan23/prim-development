<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePointHistoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('point_history', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->unsignedBigInteger('referral_code_id');
            $table->unsignedBigInteger('transaction_id');

            $table->boolean('isDebit');
            $table->boolean('fromSubline');
            $table->boolean('status');
            $table->decimal('points',10,2);

            $table->foreign('referral_code_id')->references('id')->on('referral_code');
            $table->foreign('transaction_id')->references('id')->on('transactions');


        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('point_history');
    }
}


/*DELIMITER //
CREATE TRIGGER transaction_status_update_trigger
AFTER UPDATE ON transactions
FOR EACH ROW
BEGIN
 DECLARE done INT DEFAULT FALSE;
DECLARE pointHistoryId INT;
DECLARE cur CURSOR FOR
   SELECT id FROM point_history WHERE transaction_id = Old.id;

DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;
    IF OLD.status <> NEW.status and NEW.status = "Success" THEN
        OPEN cur;
	        cursor_loop: LOOP
	            FETCH cur INTO pointHistoryId;
	            
	            IF done THEN
	                LEAVE cursor_loop;
	            END IF;
	            
	           IF pointHistoryId IS NOT NULL THEN
	                UPDATE point_history
	                SET status = 1
	                WHERE id = pointHistoryId;
	            END IF;
	        	END LOOP;
        CLOSE cur;
    END IF;
END //
DELIMITER ;*/

/*    	
DELIMITER //
CREATE TRIGGER point_history_update_trigger
AFTER UPDATE ON point_history
FOR EACH ROW
BEGIN
    DECLARE debitAmount DECIMAL(10,2);
    DECLARE creditAmount DECIMAL(10,2);

    IF OLD.status <> NEW.status THEN
        SELECT COALESCE(SUM(points), 0) INTO debitAmount
        FROM point_history 
        WHERE referral_code_id = OLD.referral_code_id AND isDebit = 1 AND status = 1;

        SELECT COALESCE(SUM(points), 0) INTO creditAmount
        FROM point_history 
        WHERE referral_code_id = OLD.referral_code_id AND isDebit = 0 AND status = 1;

        UPDATE referral_code
        SET total_point = debitAmount - creditAmount
        WHERE id = OLD.referral_code_id;
    END IF;
END //
DELIMITER ;*/