<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('trading_data', function (Blueprint $table) {
            $table->id();
            $table->string('symbol', 10);
            $table->decimal('price', 10, 4);
            $table->bigInteger('volume');
            $table->decimal('change_percent', 5, 2);
            $table->decimal('high', 10, 4);
            $table->decimal('low', 10, 4);
            $table->decimal('open', 10, 4);
            $table->decimal('close', 10, 4);
            $table->timestamp('trade_date');
            $table->timestamps();

            $table->index(['symbol', 'trade_date']);
        });

        // Insert sample trading data
        $sampleData = [
            ['AAPL', 175.25, 2500000, 2.15, 176.80, 172.30, 173.45, 175.25],
            ['GOOGL', 2450.75, 1200000, -0.85, 2465.20, 2440.10, 2460.30, 2450.75],
            ['MSFT', 315.60, 3100000, 1.75, 318.90, 312.45, 314.20, 315.60],
            ['TSLA', 195.45, 5600000, -3.25, 202.10, 193.80, 201.75, 195.45],
            ['AMZN', 3200.80, 1800000, 0.95, 3220.45, 3185.20, 3195.60, 3200.80],
            ['NVDA', 425.30, 4200000, 4.85, 430.75, 405.80, 408.20, 425.30],
            ['META', 275.90, 2800000, -1.45, 281.20, 273.50, 279.80, 275.90],
            ['NFLX', 385.70, 2200000, 2.30, 390.45, 382.15, 377.80, 385.70],
        ];

        foreach ($sampleData as $data) {
            DB::table('trading_data')->insert([
                'symbol' => $data[0],
                'price' => $data[1],
                'volume' => $data[2],
                'change_percent' => $data[3],
                'high' => $data[4],
                'low' => $data[5],
                'open' => $data[6],
                'close' => $data[7],
                'trade_date' => now()->subDays(rand(1, 30)),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trading_data');
    }
};