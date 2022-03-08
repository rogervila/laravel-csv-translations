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

// ...

'providers' => [
        
        // ...
        
        // Illuminate\Translation\TranslationServiceProvider::class,
        LaravelCSVTranslations\TranslationServiceProvider::class,
        
        // ...
],
```

**Create a `lang.csv` file placed on the `lang` folder**, which has a different route depending on the Laravel version.

```php
// Laravel 8.x
resources/lang/lang.csv

// Laravel 9.x
lang/lang.csv
```

Translations will be loaded from the CSV file if it exists. Otherwise, Laravel's built-in translation system will handle them.


## Configuration

The package allows to configure some of its features.

**There is no config file published by the package**. You might create it to override the package defaults:

```php
<?php

// config/lang.php

return [
    'csv' => [
        'file' => [
            'name' => 'lang.csv',
            'length' => 0,
            'separator' => ',',
        ],
        'throw_missing_file_exception' => false,
        'cache' => [
            'store' =>  array',
            'key' => \LaravelCSVTranslations\CSVLoader::class,
            'seconds' => 0,
        ],
    ]
];
```


## CSV Format

The `lang.csv` should have **keys on the first column**, and then **one column per locale** with it's [ISO 639-1 code](https://en.wikipedia.org/wiki/List_of_ISO_639-1_codes) as a header.

| keys                   | custom column                                                 | en                  | es                  | ca              |
|------------------------|---------------------------------------------------------------|---------------------|---------------------|-----------------|
| greetings.good_morning | Columns that do not match the current locale are just ignored | Good morning :name! | Buenos días, :name! | Bon dia, :name! |


## CSV File Features

The CSV file is quite flexible. These are some of it's features:


### Dimensions

Laravel's PHP translation array files allow to have more than one dimension that can be accessed with dots.

The CSV only allows **one dimension, but it allows to use dots**, as shown on the CSV Format example.


### Translation keys column

While keys must be placed on the first column, it's header content does not matter, so it's **not necessary to name it "keys"**.


### Column order

Except for the translation keys column, **the order does not matter**, so you can have N custom coluns between locale columns if you need them.


### Custom columns

Sometimes, business wants to have additional columns for translation files, like the view where a translation is placed, it's context, etc.

**You can have as much columns as you need, placed in the order you need**.


## Author

Created by [Roger Vilà](https://rogervila.es)


## License

This package is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

Package icon made by <a href="https://www.flaticon.com/free-icons/csv">Freepik - Flaticon</a>
