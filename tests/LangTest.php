<?php

use LaravelCSVTranslations\TranslationServiceProvider;
use Orchestra\Testbench\TestCase;

final class LangTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        copy(__DIR__ . '/stubs/lang.csv', app()->langPath() . '/lang.csv');
    }

    /**
     * {@inheritDoc}
     */
    protected function overrideApplicationBindings($app)
    {
        return [
            'Illuminate\Translation\TranslationServiceProvider' => TranslationServiceProvider::class,
        ];
    }

    public function test_example_returns_true(): void
    {
        // dd(trans('greetings.good_morning', ['name' => 'John']));
        $this->assertTrue(true);
    }
}
