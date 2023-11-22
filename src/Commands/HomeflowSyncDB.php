<?php

namespace Homeflow\Sync\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class HomeflowSyncDB extends Command {

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
      $local_database = DB::connection(config('sync.connection_local'));
      $tables = $local_database->select('SHOW TABLES');

      foreach ($tables as $table) {
        $table_name = $table->{'Tables_in_' . config('sync.database')};
        $sync_tables = in_array($table_name, config('sync.sync_tables'), TRUE);

        /**
         * Update by custom column.
         */
        if (isset($table_name) && !$sync_tables) {
          if (Schema::hasColumn($table_name, config('sync.column_sync'))) {
            $maxId = $local_database->table($table_name)
              ->max(config('sync.column_sync'));
            if (!empty($maxId)) {
              $data_result = $live_database->table($table_name)
                ->where(config('sync.column_sync'), '>', $maxId)
                ->get();

              if (count($data_result) > 0) {
                foreach ($data_result as $data) {
                  $local_database->table($table_name)->insert((array) $data);
                  printf('Sync table: %s - id: %s' . PHP_EOL, $table_name, $data->id);
                }
              }
            }
          }

          /**
           * Update for column update_at.
           */
          if (config('sync.column_sync_for_updated_at')) {
            $maxUpdateAt = $local_database->table($table_name)
              ->max(config('sync.updated_at'));
            if (!empty($maxUpdateAt)) {
              $data_result = $live_database->table($table_name)
                ->where(config('sync.updated_at'), '>', $maxUpdateAt)
                ->get();

              if (count($data_result) > 0) {
                foreach ($data_result as $data) {
                  $local_database->table($table_name)
                    ->where(config('sync.column_sync'), $data->id)
                    ->update((array) $data);
                  printf('Update table: %s - id: %s' . PHP_EOL, $table_name, $data->created_at);
                }
              }
            }
          }
        }
      }
    }
  }

}
