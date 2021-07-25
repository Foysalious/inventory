<?php namespace App\Services\DataMigration\migrations;

use App\Models\Channel;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AddChannelsInChannelsTable extends DataMigrationBase implements DataMigrationInterface
{
    /**
     * Run the migrations.
     *
     * @return void | string
     */
    public function handle()
    {
        $channels = [
            [
                'id' => 5,
                'name' => 'pos',
                'created_at' => Carbon::now(),
                'created_by_name' => 'automatic'
            ],
            [
                'id' => 6,
                'name' => 'webstore',
                'created_at' => Carbon::now(),
                'created_by_name' => 'automatic'
            ]
        ];
        DB::table('channels')->insert($channels);
        dump('channels data migrated successfully');
    }
}
