<?php

namespace Chengkangzai\ApuSchedule;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class ApuHoliday
{
    public const baseUrl = 'https://api.apiit.edu.my/transix-v2/holiday/active';

    private const CACHE_KEY = 'apu_holidays_data';
    private const CACHE_TTL = 24 * 60 * 60; // 24 hours

    public static function getRaw(): Collection
    {
        return Cache::remember(
            key: self::CACHE_KEY,
            ttl: self::CACHE_TTL,
            callback: fn() => Http::get(self::baseUrl)->collect()
        );
    }

    public static function getByYear(int $year): Collection
    {
        return self::getRaw()
            ->where('year', $year)
            ->pluck('holidays')
            ->flatten(1);
    }

    public static function getAll(): Collection
    {
        return self::getRaw()
            ->pluck('holidays')
            ->flatten(1);
    }

    /**
     * Clear the holidays cache
     *
     * @return bool
     */
    public static function clearCache(): bool
    {
        return Cache::forget(self::CACHE_KEY);
    }
}
