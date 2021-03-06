<?php

namespace Gause\ImageableLaravel\Tests;

use Orchestra\Testbench\TestCase;

class LaravelTestCase extends TestCase
{
    public function getPackageProviders($app)
    {
        return [
            \Gause\ImageableLaravel\ImageableLaravelServiceProvider::class,
        ];
    }

    public function getPackageAliases($app)
    {
        return [
            'Image' => \Gause\ImageableLaravel\Facades\Image::class,
        ];
    }

    public function getEnvironmentSetUp($app)
    {
        include_once __DIR__.'/../database/migrations/create_images_table.php.stub';

        (new \CreateImagesTable())->up();
    }
}
