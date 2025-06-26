<?php

namespace App\Providers;

use App\Observers\ModelActivityObserver;
use App\Services\DailyTimeRecordService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\File;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(DailyTimeRecordService::class, function ($app) {
            return new DailyTimeRecordService();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {

        view()->share('provider', config('meta')[env('APP_PROVIDER')]);

        
        $except = [
            'EmployeeTimelogs'
        ];

        $modelsPath = app_path('Models');
        if (File::exists($modelsPath)) {
            foreach (File::files($modelsPath) as $file) {
                $modelName = pathinfo($file->getFilename(), PATHINFO_FILENAME);
                if (in_array($modelName, $except)) {
                    continue;
                }

                $model = 'App\\Models\\' . $modelName;
                if (class_exists($model) && is_subclass_of($model, Model::class)) {
                    $model::observe(ModelActivityObserver::class);
                }
            }
        }
    }
}
