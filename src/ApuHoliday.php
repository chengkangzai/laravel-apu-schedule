<?php

namespace Chengkangzai\ApuSchedule;

use Chengkangzai\ApuSchedule\Data\HolidayData;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Spatie\LaravelData\DataCollection;

class ApuHoliday
{
    public const BASE_URL = 'https://api.apiit.edu.my/transix-v2/holiday/active';
    private const CACHE_KEY = 'apu_holidays_data';
    private const CACHE_TTL = 24 * 60 * 60; // 24 hours

    public static function getRaw(): Collection
    {
        return Cache::remember(
            key: self::CACHE_KEY,
            ttl: self::CACHE_TTL,
            callback: fn() => Http::get(self::BASE_URL)->collect()
        );
    }

    /**
     * Get holidays by year as a collection of HolidayData objects
     *
     * @param int $year
     * @return DataCollection
     */
    public static function getByYear(int $year): DataCollection
    {
        $holidays = self::getRaw()
            ->where('year', $year)
            ->pluck('holidays')
            ->flatten(1)
            ->map(fn ($holiday) => HolidayData::fromArray($holiday));

        return new DataCollection(HolidayData::class, $holidays);
    }

    /**
     * Get all holidays as a collection of HolidayData objects
     *
     * @return DataCollection
     */
    public static function getAll(): DataCollection
    {
        $holidays = self::getRaw()
            ->pluck('holidays')
            ->flatten(1)
            ->map(fn ($holiday) => HolidayData::fromArray($holiday));

        return new DataCollection(HolidayData::class, $holidays);
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
