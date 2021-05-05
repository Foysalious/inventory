<?php


namespace Tests\Feature;


use App\Models\Partner;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Schema;
use phpDocumentor\Reflection\Types\Void_;
use Tests\TestCase;

class FeatureTestCase extends TestCase
{
    use DatabaseMigrations;
    /**
     * A basic test example.
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
    }

    public function get($uri, array $headers = [])
    {
        $uri = trim($this->baseUrl, '/') . '/' . trim($uri, '/');
        return parent::get($uri, $headers);
    }

    public function post($uri, array $data = [], array $headers = [])
    {
        $uri = trim($this->baseUrl, '/') . '/' . trim($uri, '/');
        return parent::post($uri, $data, $headers);
    }

    public function put($uri, array $data = [], array $headers = [])
    {
        $uri = trim($this->baseUrl, '/') . '/' . trim($uri, '/');
        return parent::put($uri, $data, $headers);
    }

    /**
     * Define hooks to migrate the database before and after each test.
     *
     * @return void
     */

    public function runDatabaseMigrations()
    {
        // \Illuminate\Support\Facades\DB::unprepared(file_get_contents('database/seeds/sheba_testing.sql'));
         $this->artisan('migrate');
        /* $this->beforeApplicationDestroyed(function () {
             \Illuminate\Support\Facades\DB::unprepared(file_get_contents('database/seeds/sheba_testing.sql'));
         });*/
    }
    protected function createPartner()
    {
        $this->truncateTables([
            Partner::class
        ]);
        $this->partner =  Partner::factory()->create();
        dd($this->partner);
    }
    protected function truncateTable($table)
    {
        $this->truncateTables([
            $table
        ]);
    }

    protected function truncateTables(array $tables)
    {
        Schema::disableForeignKeyConstraints();
        foreach ($tables as $table) {
            $table::truncate();
        }
        Schema::enableForeignKeyConstraints();
    }

}
