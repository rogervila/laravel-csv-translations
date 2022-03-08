<?php

use LaravelCSVTranslations\CSVLoader;
use Orchestra\Testbench\TestCase;

final class LangTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // TODO: check why overrideApplicationBindings is not working
        app()->singleton('translation.loader', function ($app) {
            return new CSVLoader($app['files'], $app['path.lang']);
        });

        $this->loadStubs();
    }

    protected function loadStubs(): void
    {
        copy(__DIR__ . '/stubs/lang.csv', app()->langPath() . '/lang.csv');
        copy(__DIR__ . '/stubs/ca.json', app()->langPath() . '/ca.json');
    }

    /**
     * {@inheritDoc}
     */
    protected function overrideApplicationBindings($app)
    {
        return [
            'Illuminate\Translation\TranslationServiceProvider' => 'LaravelCSVTranslations\TranslationServiceProvider',
        ];
    }

    public function test_csv_localization(): void
    {
        app()->setLocale('en');

        $this->assertEquals(
            trans('greetings.good_morning', ['name' => 'John']),
            'Good morning John!'
        );

        app()->setLocale('es');

        $this->assertEquals(
            trans('greetings.good_morning', ['name' => 'Juan']),
            'Buenos días, Juan!'
        );

        app()->setLocale('ca');

        $this->assertEquals(
            trans('greetings.good_morning', ['name' => 'Joan']),
            'Bon dia, Joan!'
        );
    }

    public function test_skips_translation_if_not_found_on_csv_file(): void
    {
        app()->setLocale('ca');

        $this->assertEquals(
            trans('example'),
            'example'
        );
    }

    public function test_skips_csv_if_file_not_found(): void
    {
        rename(app()->langPath() . '/lang.csv', app()->langPath() . '/lang.csv.bak');

        app()->setLocale('ca');

        $this->assertEquals(
            trans('example'),
            'Aquest exemple està escrit en Català' // loaded from ca.json
        );
    }
}
