<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class AddProducedStatusToRequestsTable extends Migration
{
    public function up()
    {
        // First get the current enum values
        $currentEnumValues = DB::select("SHOW COLUMNS FROM requests WHERE Field = 'status'")[0]->Type;
        preg_match("/^enum\((.*)\)$/", $currentEnumValues, $matches);
        $values = str_getcsv($matches[1], ",", "'");
        
        // Add 'produced' if it doesn't exist
        if (!in_array('produced', $values)) {
            $values[] = 'produced';
            $enumString = "'" . implode("','", $values) . "'";
            DB::statement("ALTER TABLE requests MODIFY COLUMN status ENUM($enumString) NOT NULL DEFAULT 'pending'");
        }
    }

    public function down()
    {
        // Get current enum values
        $currentEnumValues = DB::select("SHOW COLUMNS FROM requests WHERE Field = 'status'")[0]->Type;
        preg_match("/^enum\((.*)\)$/", $currentEnumValues, $matches);
        $values = str_getcsv($matches[1], ",", "'");
        
        // Remove 'produced' value
        $values = array_filter($values, function($value) {
            return $value !== 'produced';
        });
        
        $enumString = "'" . implode("','", $values) . "'";
        DB::statement("ALTER TABLE requests MODIFY COLUMN status ENUM($enumString) NOT NULL DEFAULT 'pending'");
    }
}
