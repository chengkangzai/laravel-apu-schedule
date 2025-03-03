<?php

namespace Chengkangzai\ApuSchedule;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class ApuSchedule
{
    public const baseUrl = 'https://s3-ap-southeast-1.amazonaws.com/open-ws/weektimetable';

    private const CACHE_KEY = 'apu_schedule_data';
    private const CACHE_TTL = 24 * 60 * 60; // 24 hours

    public static function getIntakes(): Collection
    {
        return self::get()
            ->pluck('INTAKE')
            ->unique()
            ->sort();
    }

    public static function getGroupings($intake): Collection
    {
        return self::get()
            ->where('INTAKE', $intake)
            ->sort()
            ->pluck('GROUPING')
            ->unique();
    }

    public static function getMODID($intake, $grouping): Collection
    {
        return self::get()
            ->where('INTAKE', $intake)
            ->where('GROUPING', $grouping)
            ->pluck('MODID');
    }

    public static function getByIntake($intake): Collection
    {
        return self::get()
            ->where('INTAKE', $intake);
    }

    /*
     * Get the schedule for a given intake and grouping
     * @param string $intake
     * @param string $grouping
     * @param array $ignore - optional, if set, ignore this course. It must be exactly the same with MODID
     * @return Collection
     */
    public static function getSchedule($intake, $grouping, $ignore = []): Collection
    {
        return self::get()
            ->where('INTAKE', $intake)
            ->where('GROUPING', $grouping)
            ->when($ignore, fn ($schedule) => $schedule->whereNotIn('MODID', $ignore));
    }

    public static function get(): Collection
    {
        return Cache::remember(
            key: self::CACHE_KEY,
            ttl: self::CACHE_TTL,
            callback: fn() => Http::get(self::baseUrl)->collect()
        );
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
