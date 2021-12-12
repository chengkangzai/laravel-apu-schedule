<?php

namespace Chengkangzai\ApuSchedule;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;

class ApuSchedule
{
    public const baseUrl = 'https://s3-ap-southeast-1.amazonaws.com/open-ws/weektimetable';

    public static function getIntakes(): Collection
    {
        return self::get()->pluck('INTAKE')->unique()->sort();
    }

    public static function getGroupings($intake = null): Collection
    {
        if ($intake) {
            return self::get()->where('INTAKE', $intake)->sort()->pluck('GROUPING')->unique();
        }

        return self::get()->pluck('GROUPING')->unique();
    }

    public static function getByIntake($intake): Collection
    {
        return self::get()->where('INTAKE', $intake);
    }

    public static function getSchedule($intake, $grouping): Collection
    {
        return self::get()->where('INTAKE', $intake)->where('GROUPING', $grouping);
    }

    public static function get(): Collection
    {
        return collect(json_decode(Http::get(self::baseUrl)));
    }
}
