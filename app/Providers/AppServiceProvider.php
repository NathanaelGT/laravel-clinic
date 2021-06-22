<?php

namespace App\Providers;

use Carbon\Carbon;
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
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Carbon::setLocale('id');
        Paginator::useBootstrap();

        if (config('app.debug')) {
            $firstTime = true;
            DB::listen(function ($query) use (&$firstTime) {
                $log = array_map(function ($bind) {
                    try {
                        return (int) $bind == $bind ? $bind : "'$bind'";
                    } catch (\Exception) {
                        return "'$bind'";
                    }
                }, $query->bindings);

                $data = '';
                if ($firstTime) {
                    $firstTime = false;
                    $data = "\n[" . date('Y-m-d H:i:s') . "]\n";
                }
                $data .= sprintf(str_replace('?', '%s', $query->sql), ...$log) . "\n";

                File::append(storage_path('/logs/query.log'), $data);
            });
        }
    }
}
