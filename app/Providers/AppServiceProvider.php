<?php

namespace App\Providers;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        if ($this->app->isLocal()) {
            $this->app->register(\Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider::class);
        }
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        \Carbon\Carbon::setLocale('id');
        Paginator::useBootstrap();

        if (env('APP_DEBUG')) {
            $firstTime = true;
            DB::listen(function ($query) use (&$firstTime) {
                File::append(
                    storage_path('/logs/query.log'),
                    ($firstTime ? "\n[" . date('Y-m-d H:i:s') . "]\n" : '') .
                    sprintf(
                        str_replace('?', '%s', $query->sql),
                        ...array_map(
                            function ($bind) {
                                try {
                                    return (int) $bind == $bind ? $bind : "'$bind'";
                                }
                                catch (\Exception $ignored) {
                                    return "'$bind'";
                                }
                            },
                            $query->bindings
                        )
                    ) .
                    "\n"
                );

                $firstTime = false;
            });
        }
    }
}
