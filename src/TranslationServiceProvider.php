<?php

namespace LaravelCSVTranslations;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Translation\TranslationServiceProvider as BaseTranslationServiceProvider;

class TranslationServiceProvider extends BaseTranslationServiceProvider
{
    protected function registerLoader(): void
    {
        $this->app->singleton(
            CSVLocalFileResolver::class,
            static fn (Application $app): CSVLocalFileResolver => new CSVLocalFileResolver(path: $app['path.lang'])
        );

        $this->app->singleton(
            'translation.loader',
            static fn (Application $app): CSVLoader => new CSVLoader($app['files'], $app['path.lang'])
        );
    }
}
