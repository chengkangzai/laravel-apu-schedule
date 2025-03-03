<?php

namespace Chengkangzai\ApuSchedule;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;

class ApuHoliday
{
    public const baseUrl = 'https://api.apiit.edu.my/transix-v2/holiday/active';

    public static function getRaw(): Collection
    {
        return collect(Http::get(self::baseUrl)->json());
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
}
