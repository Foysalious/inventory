<?php

namespace Tests;

use Dotenv\Dotenv;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Foundation\Bootstrap\LoadConfiguration;

trait CreatesApplication
{
    /**
     * The base URL to use while testing the application.
     *
     * @var string
     */
    protected $baseUrl;
    /**
     * Creates the application.
     *
     * @return \Illuminate\Foundation\Application
     */
    public function createApplication()
    {
        $app = require __DIR__.'/../bootstrap/app.php';

        $app->make(Kernel::class)->bootstrap();
        $this->afterApplicationCreated(function () {
            $this->artisan('config:clear');
        });
     //  (new Dotenv($app->environmentPath(), $app->environmentFile()))->overload();
        (new LoadConfiguration())->bootstrap($app);

        $this->baseUrl = env('APP_URL');

        return $app;
    }
}
