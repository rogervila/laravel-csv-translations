<p align="center"><img width="200" src="https://i.ibb.co/VmZYSSM/csv.png" alt="Laravel CSV Translations" /></p>

[![Build Status](https://github.com/rogervila/laravel-csv-translations/workflows/build/badge.svg)](https://github.com/rogervila/laravel-csv-translations/actions)
[![StyleCI](https://github.styleci.io/repos/211657121/shield?branch=main)](https://github.styleci.io/repos/211657121)
[![Quality Gate Status](https://sonarcloud.io/api/project_badges/measure?project=rogervila_laravel-csv-translations&metric=alert_status)](https://sonarcloud.io/dashboard?id=rogervila_laravel-csv-translations)

[![Latest Stable Version](https://poser.pugx.org/rogervila/laravel-csv-translations/v/stable)](https://packagist.org/packages/rogervila/laravel-csv-translations)
[![Total Downloads](https://poser.pugx.org/rogervila/laravel-csv-translations/downloads)](https://packagist.org/packages/rogervila/laravel-csv-translations)
[![License](https://poser.pugx.org/rogervila/laravel-csv-translations/license)](https://packagist.org/packages/rogervila/laravel-csv-translations)

# Laravel CSV Translations

Load Laravel localizations from a CSV File

## Installation

```sh
composer require rogervila/laravel-csv-translations
```

To use Laravel CSV Translations you will have to **replace the Laravel TranslationServiceProvider with the package one**.

```php
// config/app.php
'providers' => [
    // ...
    // Illuminate\Translation\TranslationServiceProvider::class,
    LaravelCSVTranslations\TranslationServiceProvider::class,
    // ...
],
```

If your project uses `Illuminate\Support\ServiceProvider`, replace it via the `replace` method.

```php
// config/app.php
'providers' => ServiceProvider::defaultProviders()
    ->replace([
        \Illuminate\Translation\TranslationServiceProvider::class => \LaravelCSVTranslations\TranslationServiceProvider::class,
    ])->merge([
        // ...
    ])->toArray(),
```

To make it work without modifying any configuration, **Create a `lang.csv` file placed in the `lang` folder**.

Translations will be loaded from the CSV file if it exists. Otherwise, Laravel's built-in translation system will handle them.


## Configuration

The package allows configuring some of its features.

**There is no config file published by the package**. You might create it to override the package defaults:

```php
<?php

// config/lang.php

return [
    'csv' => [
        'enabled' => (bool) env('CSV_TRANSLATIONS_ENABLED', true),
        // You might use a custom resolver to get CSV data from elsewhere
        'resolver' => \LaravelCSVTranslations\CSVLocalFileResolver::class,
        'throw_missing_file_exception' => false,
        'cache' => [
            'key' => \LaravelCSVTranslations\CSVLoader::class,
            'store' =>  'array',
            'seconds' => 0,
        ],
    ]
];
```

## CSV format

The CSV data should have **keys on the first column**, and then **one column per locale** with its [ISO 639-1 code](https://en.wikipedia.org/wiki/List_of_ISO_639-1_codes) as a header.

| keys                   | custom column                                                 | en                  | es                  | ca              |
|------------------------|---------------------------------------------------------------|---------------------|---------------------|-----------------|
| greetings.good_morning | Columns that do not match the current locale are just ignored | Good morning :name! | Buenos días, :name! | Bon dia, :name! |


## CSV features

The CSV format is quite flexible. These are some of its features:


### Dimensions

Laravel's PHP translation array files allow having more than one dimension that can be accessed with dots.

The CSV only allows **one dimension, but it allows to use dots**, as shown in the CSV Format example.


### Translation keys column

While keys must be placed on the first column, its header content does not matter, so it's **not necessary to name it "keys"**.


### Column order

Except for the translation keys column, **the order does not matter**, so you can have N custom columns between locale columns if you need them.


### Custom columns

Sometimes, business wants to have additional columns for translation files, like the view where a translation is placed, its context, etc.

**You can have as many columns as you need, placed in the order you need**.


## CSV data resolver

By default, the package uses the `CSVLocalFileResolver` class that will try to load a `lang.csv` file from the project's `lang` path.

You may create your own CSV Resolver to customize the way to get the data:

```php
<?php

// config/lang.php

return [
    'csv' => [
        'resolver' => \App\Lang\RemoteCSVFileResolver::class,
    ]
];

// app/Lang/RemoteCSVFileResolver.php

namespace App\Lang;

use LaravelCSVTranslations\CSVResolverInterface;

class RemoteCSVFileResolver implements CSVResolverInterface
{
    public function resolve(): array
    {
        // Return the CSV formatted data
    }
}
```

## Access raw data

Sometimes it is useful to access the raw data to list all available translation keys and their values. 

To do so, `CSVLoader` comes with a handy `raw` method that returns an associative array with all translation keys and their raw values.

```php
// If TranslationServiceProvider is correctly configured, 'translation.loader' should be an instance of CSVLoader 

/** @var CSVLoader $loader */
$loader = $this->app['translation.loader'];

// Raw method returns an associative array with all translation keys and their raw values
$raw = $loader->raw('ca') 
/*
[
  "greetings.good_morning" => "Bon dia, :name!",
  // ...
]
*/
```

## Author

Created by [Roger Vilà](https://rogervila.es)


## License

This package is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

Package icon made by <a href="https://www.flaticon.com/free-icons/csv">Freepik - Flaticon</a>
