<?php

require 'vendor/autoload.php';

use App\Core\DB;
use App\Migrations\CreateVacationsTable;
use App\Migrations\CreateUsersTable;

$pdo = DB::connect();
$migrations = [new CreateUsersTable, new CreateVacationsTable];

foreach ($migrations as $migration) {
    $migration->up($pdo);
}

echo "Migrations done.\n";
