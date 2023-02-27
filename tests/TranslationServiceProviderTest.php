<?php

use PHPUnit\Framework\TestCase;
use Illuminate\Contracts\Foundation\Application;
use LaravelCSVTranslations\TranslationServiceProvider;

final class TranslationServiceProviderTest extends TestCase
{
    public function test_register_loader(): void
    {
        $this->assertInstanceOf(Application::class, $app = $this->createMock(Application::class));
        $provider = new TranslationServiceProvider($app);
        $provider->register();
    }
}
