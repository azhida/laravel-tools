<?php

namespace Azhida\Tools;

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
}