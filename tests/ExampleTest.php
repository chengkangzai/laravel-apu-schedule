<?php

use Chengkangzai\ApuSchedule\ApuSchedule;

it('can fetch from S3', function () {
    $collection = ApuSchedule::get();
    expect($collection)->toBeCollection();
    expect($collection)->not->toBeEmpty();
});

it('can get grouping', function () {
    $intake = APUSchedule::getIntakes()->random();
    $collection = APUSchedule::getGroupings($intake);
    expect($collection)->toBeCollection();
    expect($collection)->not->toBeEmpty();
});

it('can get intake', function () {
    $collection = APUSchedule::getIntakes();
    expect($collection)->toBeCollection();
    expect($collection)->not->toBeEmpty();
});

it('can get grouping by intake', function () {
    $intakes = APUSchedule::getIntakes()->random();
    $collection = ApuSchedule::getGroupings($intakes);
    expect($collection)->toBeCollection();
    expect($collection)->not->toBeEmpty();
});

it('can get timetable base on intake and grouping', function () {
    $intakes = APUSchedule::getIntakes()->random();
    $grouping = ApuSchedule::getGroupings($intakes)->random();
    $collection = ApuSchedule::getSchedule($intakes, $grouping);
    expect($collection)->toBeCollection();
    expect($collection)->not->toBeEmpty();
});

it('can get timetable base on intake and grouping with ignore ', function () {
    $intakes = APUSchedule::getIntakes()->random();
    $grouping = ApuSchedule::getGroupings($intakes)->random();
    $MODID = ApuSchedule::getSchedule($intakes, $grouping)->random()->MODID;
    $scheduleWFilter = ApuSchedule::getSchedule($intakes, $grouping, [$MODID]);
    expect($scheduleWFilter)->toBeCollection();
    expect($scheduleWFilter)->not->toBeEmpty();

    $scheduleWOFilter = ApuSchedule::getSchedule($intakes, $grouping);
    expect($scheduleWOFilter)->toBeCollection();
    expect($scheduleWOFilter)->not->toBeEmpty();

    expect($scheduleWFilter->count())->toBeLessThan($scheduleWOFilter->count());
});
