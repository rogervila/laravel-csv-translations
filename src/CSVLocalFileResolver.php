<?php

namespace LaravelCSVTranslations;

use RuntimeException;

class CSVLocalFileResolver implements CSVResolverInterface
{
    public function __construct(
        protected string $path,
        protected string $filename = 'lang.csv',
        protected string $separator = ',',
        protected int $length = 0,
    ) {
    }

    /**
     * Get CSV data from a "lang.csv" file placed on the specified path
     *
     * @return mixed[]
     */
    public function resolve(): array
    {
        $data = [];

        if (
            !file_exists($path = $this->path . '/' . $this->filename) ||
            ($handle = fopen($path, 'r')) === false
        ) {
            throw_if(config('lang.csv.throw_missing_file_exception', false), new RuntimeException("Translation file [{$path}] could not be loaded."));
            return [];
        }

        while (($row = fgetcsv($handle, $this->length, $this->separator)) !== false) {
            array_push($data, $row);
        }

        fclose($handle);

        throw_if(count($data) === 0, new RuntimeException("Translation file [{$path}] does not contain valid headers."));

        return $data;
    }
}
