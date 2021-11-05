<?php

namespace Ldavidsp\Sync;

use Illuminate\Support\ServiceProvider;
use Ldavidsp\Sync\Commands\SyncDB;

class SyncServiceProvider extends ServiceProvider {

  /**
   * Bootstrap any package services.
   *
   * @return void
   */
  public function boot() {
    if ($this->app->runningInConsole()) {
      $this->commands([
        SyncDB::class,
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

