<p align="center"><img width="200" src="https://i.ibb.co/VmZYSSM/csv.png" alt="Laravel CSV Translations" /></p>

[![Build Status](https://github.com/rogervila/laravel-csv-translations/workflows/build/badge.svg)](https://github.com/rogervila/laravel-csv-translations/actions)
[![StyleCI](https://github.styleci.io/repos/211657121/shield?branch=main)](https://github.styleci.io/repos/211657121)
[![Quality Gate Status](https://sonarcloud.io/api/project_badges/measure?project=rogervila_laravel-csv-translations&metric=alert_status)](https://sonarcloud.io/dashboard?id=rogervila_laravel-csv-translations)

[![Latest Stable Version](https://poser.pugx.org/rogervila/laravel-csv-translations/v/stable)](https://packagist.org/packages/rogervila/laravel-csv-translations)
[![Total Downloads](https://poser.pugx.org/rogervila/laravel-csv-translations/downloads)](https://packagist.org/packages/rogervila/laravel-csv-translations)
[![License](https://poser.pugx.org/rogervila/laravel-csv-translations/license)](https://packagist.org/packages/rogervila/laravel-csv-translations)

# Laravel CSV Translations

## Installation

```sh
composer require rogervila/laravel-csv-translations
```

## Configuration

To use Laravel CSV Translations you will have to replace the Laravel TranslationServiceProvider with the package one.

```php
// config/app.php

// ...

'providers' => [
        
        // ...
        
        // Illuminate\Translation\TranslationServiceProvider::class,
        LaravelCSVTranslations\TranslationServiceProvider::class,
        
        // ...
],
```

Create a `lang.csv` file placed on the `lang` folder, which has a different route depending on the Laravel version.

```php
// Laravel 8.x
resources/lang/lang.csv

// Laravel 9.x
lang/lang.csv
```

Translations will be loaded from the CSV file if it exists. Otherwise, Laravel's built-in translation system will handle them.


## Author

Created by [Roger Vilà](https://rogervila.es)

## License

This package is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

Package icon made by <a href="https://www.flaticon.com/free-icons/csv">Freepik - Flaticon</a>
