<?php

namespace Chengkangzai\ApuSchedule\Commands;

use Illuminate\Console\Command;

class ApuScheduleCommand extends Command
{
    public $signature = 'laravel-apu-schedule';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
