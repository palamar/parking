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
        Schema::create('zones', function (Blueprint $table) {
            $table->id()->autoIncrement();
            $table->string('code')
                ->unique(true)
                ->comment('human readable parking zones like A, B, C');
            $table->integer('rank')
                ->unsigned()
                ->comment('used to allow parking in the lower zone after the fee in the higher one');
            $table->timestamps();
            $table->string('note', 1000)->nullable();
            $table->comment('parking zones of the city');
        });

        Schema::create('scanners', function (Blueprint $table) {
            $table->id()->autoIncrement();
            $table->string('scanner_code', 200)
                ->unique(true)
                ->comment('code of the scanner, eg. S/N');
            $table->string('public_key', 1000)
                ->comment('public key of the scanner, to encrypt message to the scanner');
            $table->timestamps();
            $table->string('note', 1000)->nullable();
            $table->comment('scanners that used by operators to scan car\'s plate');
        });

        Schema::create('operators', function (Blueprint $table) {
            $table->id()->autoIncrement();
            $table->string('operator_code', 200)
                ->unique(true)
                ->comment('just in case of two with the same name, surname');
            $table->string('name', 200)
                ->comment('name of the operator, eg. John');
            $table->string('surname', 200)
                ->comment('surname of the operator, eg. Doe');
            $table->timestamps();
            $table->string('public_key', 1000)
                ->comment('public key of the operator, to encrypt message to the operator');
            $table->string('note', 1000)->nullable();
            $table->comment('operators who used scanners to scan car\'s plate');
        });

        Schema::create('price_types', function (Blueprint $table) {
            $table->id()->autoIncrement();
            $table->string('code', 200)
                ->unique(true)
                ->comment('human readable parking price types, eg. hour|day');
            $table->string('note', 200)->nullable();
            $table->timestamps();
            $table->comment('used to to diff. price types: hour, dat');
        });


        Schema::create('prices', function (Blueprint $table) {
            $table->id()->autoIncrement();
            $table->integer('zone_id')->unsigned()->references('id')->on('zones');
            $table->integer('price_type_id')->unsigned()->references('id')->on('price_types');
            $table->timestamps();
            $table->dateTimeTz('active_from')
                ->comment('we need possibility to add new prices in the future');
            $table->dateTimeTz('active_to')
                ->nullable()
                ->comment('we need possibility to hide prices in the future');
            $table->float('amount')
                ->unsigned()
                ->comment('price of the parking lot');
            $table->string('note', 1000)->nullable();
        });

        Schema::create('ownership_types', function (Blueprint $table) {
            $table->id()->autoIncrement();
            $table->string('code', 200)
                ->unique(true)
                ->comment('code of the ownership type, eg. private, municipal, police');
            $table->timestamps();
            $table->string('note', 1000)->nullable();
        });

        Schema::create('vehicles', function (Blueprint $table) {
            $table->id()->autoIncrement();
            $table->string('plate', 200)
                ->unique(true)
                ->comment('plate of the vehicle, eg. VM 123 RT');
            $table->string('region', 200)
                ->comment('region of the vehicle')
                ->nullable(true);
            $table->integer('ownership_type_id')
                ->unsigned()
                ->nullable(true)
                ->references('id')->on('ownership_types');
            $table->timestamps();
            $table->string('note', 1000)->nullable();
            $table->comment('we store a vehicle buy it\'s plate in order to track it');
        });

        // as we have ranks of the zones it's possible that we would need to add trigger in order to validate
        // zone ranks before create parking ticket for the cheaper zone
        Schema::create('fees', function (Blueprint $table) {
            $table->id()->autoIncrement();
            $table->integer('vehicle_id')->unsigned()->references('id')->on('vehicles');
            $table->integer('price_id')->unsigned()->references('id')->on('prices');
            $table->integer('scanner_id')->unsigned()->references('id')->on('scanners');
            $table->integer('operator_id')->unsigned()->references('id')->on('operators');
            $table->integer('zone_id')->unsigned()->references('id')->on('zones');
            $table->date('issue_date');
            $table->timeTz('issue_time');
            $table->float('amount')->unsigned();
            $table->date('due_date');
            $table->boolean('is_closed')->default(false);
            $table->timestamps();
            $table->string('note', 1000)->nullable();
            $table->unique(['vehicle_id', 'zone_id', 'issue_date']);
        });

        Schema::create('fee_payments', function (Blueprint $table) {
            $table->id()->autoIncrement();
            $table->integer('fee_id')->unsigned()->references('id')->on('fees');
            $table->float('amount')->unsigned();
            $table->timestamps();
            $table->string('payment_details', 1000)->nullable(true);
            $table->string('note', 1000)->nullable(true);
        });

        Schema::create('payments', function (Blueprint $table) {
            $table->id()->autoIncrement();
            $table->integer('vehicle_id')->unsigned()->references('id')->on('vehicles');
            $table->integer('price_id')->unsigned()->references('id')->on('prices');
            $table->integer('zone_id')->unsigned()->references('id')->on('zones');
            $table->float('amount')->unsigned();
            $table->dateTimeTz('start_time');
            $table->dateTimeTz('end_time');
            $table->timestamps();
            $table->string('note', 1000)
                ->nullable(true)
                ->comment('store parking payments to identify is parking slot has been payed');
        });

        Schema::create('request_logs', function (Blueprint $table) {
            $table->id()->autoIncrement();
            $table->integer('scanner_id')->unsigned();
            $table->integer('operator_id')->unsigned();
            $table->string('request_path', 800);
            $table->string('request_method', 50);
            $table->string('request_date', 6000);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
        Schema::dropIfExists('fee_payments');
        Schema::dropIfExists('fees');
        Schema::dropIfExists('vehicles');
        Schema::dropIfExists('ownership_types');
        Schema::dropIfExists('prices');
        Schema::dropIfExists('price_types');
        Schema::dropIfExists('operators');
        Schema::dropIfExists('scanners');
        Schema::dropIfExists('zones');
        Schema::dropIfExists('request_logs');
    }
};
