<?php

namespace Chengkangzai\ApuSchedule\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Chengkangzai\ApuSchedule\ApuSchedule
 */
class ApuSchedule extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'laravel-apu-schedule';
    }
}
