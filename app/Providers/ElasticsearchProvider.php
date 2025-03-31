<?php

namespace App\Providers;

use Elastic\Elasticsearch\ClientBuilder;
use Illuminate\Support\ServiceProvider;

class ElasticsearchProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('Elasticsearch', function () {
            $config = config('elasticsearch');
            return ClientBuilder::create()
                ->setHosts($config['hosts'])
                ->build();
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
