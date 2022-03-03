<?php

namespace LaravelCSVTranslations;

use Illuminate\Support\Facades\Cache;
use Illuminate\Translation\FileLoader;
use RuntimeException;

class CSVLoader extends FileLoader
{
    /**
     * Load the messages for the given locale.
     *
     * @param  string  $locale
     * @param  string  $group
     * @param  string|null  $namespace
     * @return array
     */
    public function load($locale, $group, $namespace = null)
    {
        if ($group === '*' && $namespace === '*' && count($data = $this->getDataFromCSVFile()) > 0) {
            return $this->loadLocalizedData($data, $locale);
        }

        return parent::load($locale, $group, $namespace);
    }

    /**
     * Load localized data from CSV File.
     *
     * @param  array  $data
     * @param  string  $locale
     * @return array
     */
    protected function loadLocalizedData($data, $locale)
    {
        throw_if(count($data) === 0, new RuntimeException("Translations for locale [{$locale}] headers are missing."));

        $locale_column = array_search($locale, $data[0]);

        if ($locale_column === false) {
            $locale_column = array_search(config('app.fallback_locale', 'en'), $data[0]);
        }

        if ($locale_column === false) {
            return [];
        }

        return collect($data)->flatMap(function ($row) use ($locale_column) {
            $translation_key_column = $row[0];

            return [$translation_key_column => $row[$locale_column]];
        })->except(config('lang.csv.key_column_header', 'key'))->toArray();
    }

    /**
     * Read the CSV file and return it as a multidimensional array
     *
     * @return array
     *
     * @throws \RuntimeException
     */
    protected function getDataFromCSVFile()
    {
        return Cache::remember(config('lang.csv.cache.key', self::class), config('lang.csv.cache.ttl', 0), function () {
            $data = [];

            if (
                !file_exists($path = $this->path . '/' . config('lang.csv.file_name', 'lang.csv')) ||
                ($handle = fopen($path, 'r')) === false
            ) {
                throw_if(config('lang.csv.throw_missing_file', false), new RuntimeException("Translation file [{$path}] could not be loaded."));
                return [];
            }

            while (($row = fgetcsv($handle, config('lang.csv.length', 0), config('lang.csv.separator', ','))) !== false) {
                array_push($data, $row);
            }

            fclose($handle);

            throw_if(count($data) === 0, new RuntimeException("Translation file [{$path}] does not contain valid headers."));

            return $data;
        });
    }
}
