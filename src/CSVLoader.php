<?php

namespace LaravelCSVTranslations;

use Illuminate\Support\Collection;
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
        return config('lang.csv.enabled', true) === true && $group === '*' && $namespace === '*' && count($data = $this->getCSVData()) > 0
            ? $this->loadCSVLocalizedData($data, $locale)
            : parent::load($locale, $group, $namespace);
    }

    /**
     * @param mixed[] $data
     * @return mixed[]
     *
     * @throws RuntimeException
     */
    protected function loadCSVLocalizedData(array $data, string $locale): array
    {
        throw_if(count($data) === 0, new RuntimeException("Translations for locale [{$locale}] headers are missing."));
        throw_unless(is_array($headers = $data[0]), new RuntimeException("Translations for locale [{$locale}] headers are invalid."));
        /** @psalm-var mixed[] $headers */
        $locale_column = array_search($locale, $headers);

        if ($locale_column === false) {
            $locale_column = array_search(config('app.fallback_locale', 'en'), $headers);
        }

        if ($locale_column === false) {
            return [];
        }

        return collect($data)
            ->flatMap(
                static function (mixed $row) use ($locale_column): array {
                    if ($row instanceof Collection) {
                        $row = $row->toArray();
                    }

                    if (!is_array($row)) {
                        return [];
                    }

                    /** @var mixed[] $row */
                    return count($row) > 0 && array_key_exists($locale_column, $row)
                        ? [$row[0] => $row[$locale_column]]
                        : [];
                }
            )
            ->reject(static fn (mixed $value): bool => !is_string($value) || $value === '')
            ->toArray();
    }

    /**
     * Read the CSV file and return it as a multidimensional array
     *
     * @return mixed[]
     *
     * @throws RuntimeException
     */
    protected function getCSVData(): array
    {
        /** @psalm-return mixed[] */
        return Cache::store((string) config('lang.csv.cache.store', 'array'))->remember(
            (string) config('lang.csv.cache.key', self::class),
            (int) config('lang.csv.cache.seconds', 0),
            static fn (): array => static::getResolver()->resolve(),
        );
    }

    /**
     * @throws RuntimeException
     */
    protected static function getResolver(): CSVResolverInterface
    {
        $resolver = config('lang.csv.resolver', CSVLocalFileResolver::class);

        return match (true) {
            is_string($resolver) && ($instance = app($resolver)) instanceof CSVResolverInterface => $instance,
            is_object($resolver) && $resolver instanceof CSVResolverInterface => $resolver,
            default => throw new RuntimeException("Resolver [" . get_debug_type($resolver) . "] must be an instance of " . CSVResolverInterface::class),
        };
    }

    public function raw(string $locale): array
    {
        return $this->loadCSVLocalizedData($this->getCSVData(), $locale);
    }
}
