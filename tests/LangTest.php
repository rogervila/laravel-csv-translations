<?php

use LaravelCSVTranslations\CSVLoader;
use LaravelCSVTranslations\CSVResolverInterface;
use Orchestra\Testbench\TestCase;

final class LangTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->loadStubs();
    }

    protected function loadStubs(): void
    {
        copy(__DIR__ . '/stubs/lang.csv', $this->app->langPath() . '/lang.csv');
        copy(__DIR__ . '/stubs/ca.json', $this->app->langPath() . '/ca.json');
    }

    /**
     * {@inheritDoc}
     */
    protected function overrideApplicationBindings($app)
    {
        return [
            \Illuminate\Translation\TranslationServiceProvider::class => \LaravelCSVTranslations\TranslationServiceProvider::class,
        ];
    }

    protected function getPackageProviders($app)
    {
        // overrideApplicationBindings is not working, so we need to manually register the provider
        $provider = new \LaravelCSVTranslations\TranslationServiceProvider($app);
        $provider->register();

        return [\LaravelCSVTranslations\TranslationServiceProvider::class];
    }

    public function test_csv_localization(): void
    {
        $key = 'greetings.good_morning';

        $this->app->setLocale('en');

        $this->assertEquals(
            trans($key, ['name' => 'John']),
            'Good morning John!'
        );

        $this->app->setLocale('es');

        $this->assertEquals(
            trans($key, ['name' => 'Juan']),
            'Buenos días, Juan!'
        );

        $this->app->setLocale('ca');

        $this->assertEquals(
            trans($key, ['name' => 'Joan']),
            'Bon dia, Joan!'
        );
    }

    public function test_translation_when_specifically_enabled(): void
    {
        $key = 'greetings.good_morning';
        $this->app->setLocale('ca');

        $this->app['config']->set('lang.csv.enabled', true);
        $this->assertEquals(
            trans($key, ['name' => 'Joan']),
            'Bon dia, Joan!'
        );
    }

    public function test_skips_translation_if_disabled(): void
    {
        $key = 'greetings.good_morning';
        $this->app->setLocale('ca');

        $this->app['config']->set('lang.csv.enabled', false);
        $this->assertEquals(
            $key,
            trans($key, ['name' => 'Joan'])
        );
    }

    public function test_skips_translation_if_not_found_on_csv_file(): void
    {
        $this->app->setLocale('ca');

        $this->assertEquals(
            trans('example'),
            'example'
        );
    }

    public function test_skips_csv_if_file_not_found(): void
    {
        rename($this->app->langPath() . '/lang.csv', $this->app->langPath() . '/lang.csv.bak');

        $this->app->setLocale('ca');

        $this->assertEquals(
            trans('example'),
            'Aquest exemple està escrit en Català' // loaded from ca.json
        );
    }

    public function test_skips_row_when_incomplete_or_current_locale_column_is_empty(): void
    {
        $this->app->setLocale('en');

        $this->assertEquals(
            trans('empty_row'),
            'empty_row'
        );

        $this->assertEquals(
            trans('partial_empty_row'),
            'partial_empty_row'
        );

        $this->app->setLocale('ca');

        $this->assertEquals(
            trans('partial_empty_row'),
            'barça'
        );
    }

    public function test_custom_resolver(): void
    {
        $data = [
            ['', 'en'],
            ['foo', $value = uniqid()],
        ];

        $resolver = $this->createMock(CSVResolverInterface::class);

        /** @var \PHPUnit\Framework\MockObject\MockObject $resolver */
        $resolver
            ->expects($this->any())
            ->method('resolve')
            ->willReturn($data)
        ;

        $this->app['config']->set('lang.csv.resolver', $resolver);

        $this->assertEquals(
            trans('foo'),
            $value
        );
    }

    public function test_fails_if_resolver_does_not_implement_interface(): void
    {
        $this->expectException(RuntimeException::class);
        $this->app['config']->set('lang.csv.resolver', new stdClass());

        trans('this test will fail');
    }

    public function test_access_raw_data()
    {
        $loader = $this->app['translation.loader'];
        $this->assertInstanceOf(CSVLoader::class, $loader);

        $raw = $loader->raw('es');
        $this->assertTrue(array_key_exists('keys column header content does not matter', $raw) && $raw['keys column header content does not matter'] === 'es');
        $this->assertTrue(array_key_exists('greetings.good_morning', $raw) && $raw['greetings.good_morning'] === 'Buenos días, :name!');
        $this->assertTrue(array_key_exists('partial_empty_row', $raw) && $raw['partial_empty_row'] === 'madrid');

        $raw = $loader->raw('ca');
        $this->assertTrue(array_key_exists('keys column header content does not matter', $raw) && $raw['keys column header content does not matter'] === 'ca');
        $this->assertTrue(array_key_exists('greetings.good_morning', $raw) && $raw['greetings.good_morning'] === 'Bon dia, :name!');
        $this->assertTrue(array_key_exists('partial_empty_row', $raw) && $raw['partial_empty_row'] === 'barça');

        $raw = $loader->raw('en');
        $this->assertTrue(array_key_exists('keys column header content does not matter', $raw) && $raw['keys column header content does not matter'] === 'en');
        $this->assertTrue(array_key_exists('greetings.good_morning', $raw) && $raw['greetings.good_morning'] === 'Good morning :name!');

        $raw = $loader->raw(uniqid('unknown')); // fallback lang is 'en'
        $this->assertTrue(array_key_exists('keys column header content does not matter', $raw) && $raw['keys column header content does not matter'] === 'en');
        $this->assertTrue(array_key_exists('greetings.good_morning', $raw) && $raw['greetings.good_morning'] === 'Good morning :name!');
    }
}
