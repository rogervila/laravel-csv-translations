<?php

namespace LaravelCSVTranslations;

interface CSVResolverInterface
{
    /**
     * Get CSV data
     *
     * @return mixed[]
     */
    public function resolve(): array;
}
