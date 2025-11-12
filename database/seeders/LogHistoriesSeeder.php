<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Carbon\Carbon;
use App\Models\LogHistory;

class LogHistoriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        LogHistory::create([
            'table_name' => 'orders',
            'entity_id' => 1,
            'action' => 'create',
            'user' => 'admin',
            'old_data' => null,
            'new_data' => '{"order_number":"ORD-001","total_cost":100000}',
            'timestamp' => Carbon::now(),
        ]);
    }
}
