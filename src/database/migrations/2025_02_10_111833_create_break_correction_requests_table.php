<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBreakCorrectionRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('break_correction_requests', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('attendance_correction_request_id');
            $table->foreign('attendance_correction_request_id', 'break_attendance_fk')
                ->references('id')->on('attendance_correction_requests')
                ->cascadeOnDelete();
            $table->dateTime('break_start');
            $table->dateTime('break_end');
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
        Schema::dropIfExists('break_correction_requests');
    }
}
