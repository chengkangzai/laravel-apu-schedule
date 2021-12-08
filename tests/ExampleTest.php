<?php

use Chengkangzai\ApuSchedule\ApuSchedule;

it('can fetch from S3', function () {
    $collection = ApuSchedule::get();
    expect($collection)->toBeCollection();
    expect($collection)->not->toBeEmpty();
});

it('can get grouping', function () {
    $collection = APUSchedule::getGroupings();
    expect($collection)->toBeCollection();
    expect($collection)->not->toBeEmpty();
});

it('can get intake', function () {
    $collection = APUSchedule::getAllIntakes();
    expect($collection)->toBeCollection();
    expect($collection)->not->toBeEmpty();
});

it('can get grouping by intake', function () {
    $intakes = APUSchedule::getAllIntakes()->random();
    $collection = ApuSchedule::getGroupings($intakes);
    expect($collection)->toBeCollection();
    expect($collection)->not->toBeEmpty();
});

it('can get timetable base on intake and grouping', function () {
    $intakes = APUSchedule::getAllIntakes()->random();
    $grouping = ApuSchedule::getGroupings($intakes)->random();
    $collection = ApuSchedule::getSchedule($intakes, $grouping);
    expect($collection)->toBeCollection();
    expect($collection)->not->toBeEmpty();
});
