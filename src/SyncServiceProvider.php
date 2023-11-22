<?php

namespace Homeflow\Sync;

use Illuminate\Support\ServiceProvider;
use Homeflow\Sync\Commands\SyncDB;

class SyncServiceProvider extends ServiceProvider {

  /**
   * Bootstrap any package services.
   *
   * @return void
   */
  public function boot() {
    if ($this->app->runningInConsole()) {
      $this->commands([
        HomeflowSyncDB::class,
      ]);

      $this->publishes([
        __DIR__ . '/../config/sync.php' => config_path('sync.php'),
      ], 'sync-config');
    }
  }

  /**
   * Register any application services.
   *
   * @return void
   */
  public function register() {
    $this->mergeConfigFrom(
      __DIR__ . '/../config/sync.php', 'sync'
    );
  }

}

