<?php namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

use Illuminate\Support\Facades\DB;
use Spatie\Multitenancy\Models\Tenant;

class CustomTenantModel extends Tenant
{
    use HasFactory;

    protected $table = 'tenants';

    public static function booted()
    {
        static::creating(fn(CustomTenantModel $model) => $model->createDatabase());
    }

    public function createDatabase()
    {
        $schemaName = $this->database;
        $charset = config("database.connections.mysql.charset",'utf8mb4');
        $collation = config("database.connections.mysql.collation",'utf8mb4_unicode_ci');
        config(["database.connections.mysql.database" => null]);
        $query = "CREATE DATABASE IF NOT EXISTS $schemaName CHARACTER SET $charset COLLATE $collation;";
        DB::statement($query);
        config(["database.connections.mysql.database" => $schemaName]);
    }
}
