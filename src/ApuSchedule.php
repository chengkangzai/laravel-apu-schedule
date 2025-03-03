<?php

namespace Chengkangzai\ApuSchedule;

use Chengkangzai\ApuSchedule\Data\ScheduleData;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Spatie\LaravelData\DataCollection;

class ApuSchedule
{
    public const BASE_URL = 'https://s3-ap-southeast-1.amazonaws.com/open-ws/weektimetable';
    private const CACHE_KEY = 'apu_schedule_data';
    private const CACHE_TTL = 24 * 60 * 60; // 24 hours

    /**
     * Get raw schedule data
     *
     * @return Collection
     */
    private static function getRawData(): Collection
    {
        return Cache::remember(
            key: self::CACHE_KEY,
            ttl: self::CACHE_TTL,
            callback: fn () => Http::get(self::BASE_URL)->collect()
        );
    }

    /**
     * Convert raw data to DTOs
     *
     * @return Collection
     */
    public static function get(): Collection
    {
        $rawData = self::getRawData();

        // Transform each item into a ScheduleData object
        return $rawData->map(function ($item, $key) {
            // Add the key as the ID if needed
            $item['id'] = $key;

            return ScheduleData::fromArray($item);
        });
    }

    /**
     * Get all unique intakes
     *
     * @return Collection
     */
    public static function getIntakes(): Collection
    {
        return self::get()
            ->pluck('INTAKE')
            ->unique()
            ->sort();
    }

    /**
     * Get groupings for a specific intake
     *
     * @param string $intake
     * @return Collection
     */
    public static function getGroupings(string $intake): Collection
    {
        return self::get()
            ->where('INTAKE', $intake)
            ->sort()
            ->pluck('GROUPING')
            ->unique();
    }

    /**
     * Get module IDs for a specific intake and grouping
     *
     * @param string $intake
     * @param string $grouping
     * @return Collection
     */
    public static function getMODID(string $intake, string $grouping): Collection
    {
        return self::get()
            ->where('INTAKE', $intake)
            ->where('GROUPING', $grouping)
            ->pluck('MODID');
    }

    /**
     * Get module IDs for a specific intake and grouping
     *
     * @param string $intake
     * @param string $grouping
     * @return Collection
     */
    public static function getMODIDWithName(string $intake, string $grouping): Collection
    {
        return self::get()
            ->where('INTAKE', $intake)
            ->where('GROUPING', $grouping)
            ->mapWithKeys(fn (ScheduleData $schedule) => [$schedule->MODID => $schedule->MODULE_NAME.' ('.$schedule->NAME.')']);
    }

    /**
     * Get schedule data for a specific intake
     *
     * @param string $intake
     * @return DataCollection
     */
    public static function getByIntake(string $intake): DataCollection
    {
        $schedules = self::get()
            ->where('INTAKE', $intake)
            ->values();

        return new DataCollection(ScheduleData::class, $schedules);
    }

    /**
     * Get schedule data for a specific intake and grouping
     *
     * @param string $intake
     * @param string $grouping
     * @param array $ignore
     * @return DataCollection
     */
    public static function getSchedule(string $intake, string $grouping, array $ignore = []): DataCollection
    {
        $schedules = self::get()
            ->where('INTAKE', $intake)
            ->where('GROUPING', $grouping)
            ->when($ignore, fn ($schedule) => $schedule->whereNotIn('MODID', $ignore))
            ->values();

        return new DataCollection(ScheduleData::class, $schedules);
    }

    /**
     * Clear the schedule cache
     *
     * @return bool
     */
    public static function clearCache(): bool
    {
        return Cache::forget(self::CACHE_KEY);
    }
}
