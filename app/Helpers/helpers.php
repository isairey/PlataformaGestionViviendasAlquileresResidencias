<?php

use Illuminate\Support\Str;

if (! function_exists('generate_code')) {
    function generate_code(): string
    {
        $part1 = strtoupper(Str::random(3));
        $part2 = strtoupper(Str::random(3));

        return "{$part1}-{$part2}";
    }
}

if (! function_exists('page_title')) {

    function page_title(string $title = '')
    {
        return $title ? "$title | ".config('app.name') : config('app.name');
    }
}

if (! function_exists('iso_to_us')) {
    /**
     * Convert ISO date (YYYY-MM-DD) to US format (MM/DD/YYYY)
     */
    function iso_to_us(string $isoDate): string
    {
        if (! $isoDate) {
            return '';
        }

        try {
            return \Carbon\Carbon::createFromFormat('Y-m-d', $isoDate)->format('m/d/Y');
        } catch (\Exception $e) {
            return $isoDate; // Return original if conversion fails
        }
    }
}

if (! function_exists('us_to_iso')) {
    /**
     * Convert US date (MM/DD/YYYY) to ISO format (YYYY-MM-DD)
     */
    function us_to_iso(string $usDate): string
    {
        if (! $usDate) {
            return '';
        }

        try {
            return \Carbon\Carbon::createFromFormat('m/d/Y', $usDate)->format('Y-m-d');
        } catch (\Exception $e) {
            return $usDate; // Return original if conversion fails
        }
    }
}
