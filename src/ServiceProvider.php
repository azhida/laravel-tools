<?php

namespace Azhida\LaravelTools;

class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
    protected $defer = true;

    public function register()
    {
        $this->app->singleton(Tool::class, function(){
            return new Tool();
        });

        $this->app->alias(Tool::class, 'tool');
    }

    public function provides()
    {
        return [Tool::class, 'tool'];
    }

    /**
     * 在注册后启动服务
     *
     * @return void
     */
    public function boot()
    {
//        $this->loadMigrationsFrom(__DIR__ . '/database/migrations');

        $this->publishes([
            __DIR__.'/database/migrations/2021_08_31_091229_create_test_binary_trees_table.php' => database_path('migrations/2021_08_31_091229_create_test_binary_trees_table.php'),
            __DIR__.'/database/migrations/2021_08_31_092504_create_test_binary_tree_max_depths_table.php' => database_path('migrations/2021_08_31_092504_create_test_binary_tree_max_depths_table.php'),
        ]);
    }
}