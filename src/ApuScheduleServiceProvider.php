<?php

namespace Chengkangzai\ApuSchedule;

use Chengkangzai\ApuSchedule\Commands\ApuScheduleCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class ApuScheduleServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('laravel-apu-schedule')
            ->hasConfigFile()
            ->hasViews()
            ->hasMigration('create_laravel-apu-schedule_table')
            ->hasCommand(ApuScheduleCommand::class);
    }
}
