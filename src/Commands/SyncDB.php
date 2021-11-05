<?php

namespace Ldavidsp\Sync\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class SyncDB extends Command {

  /**
   * The name and signature of the console command.
   *
   * @var string
   */
  protected $signature = 'sync:db';

  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = 'Synchronize local database with production.';

  /**
   * Create a new command instance.
   *
   * @return void
   */
  public function __construct() {
    parent::__construct();
  }

  /**
   * Execute the console command.
   */
  public function handle() {
    if (App::environment(config('sync.environment'))) {
      $live_database = DB::connection(config('sync.connection_prod'));
      $tables = DB::connection(config('sync.connection_local'))->select('SHOW TABLES');

      foreach ($tables as $table) {
        $table_name = $table->{'Tables_in_' . config('database.connections.mysql.database')};
        if (isset($table_name)) {
          if (Schema::hasColumn($table_name, config('sync.column_sync'))) {
            $maxId = DB::table($table_name)->max(config('sync.column_sync'));
            if (!empty($maxId)) {
              $data_result = $live_database->table($table_name)
                ->where(config('sync.column_sync'), '>', $maxId)
                ->get();

              if (count($data_result) > 0) {
                foreach ($data_result as $data) {
                  DB::table($table_name)->insert((array) $data);
                  printf('Sync table: %s - id: %s' . PHP_EOL, $table_name, $data->id);
                }
              }
            }
          }
        }
      }
    }
  }

}
