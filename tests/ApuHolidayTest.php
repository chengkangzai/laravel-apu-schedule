<?php

use Chengkangzai\ApuSchedule\ApuHoliday;

it('can raw', function () {
    $collection = ApuHoliday::getRaw();

    expect($collection)->toBeCollection()
        ->and($collection)->not->toBeEmpty();
});

it('can get by year', function () {
    $raw = ApuHoliday::getRaw();
    //year is not unique, so we need to get the first one
    $year = $raw->first()['year'];
    $collection = ApuHoliday::getByYear($year);

    expect($collection)->toBeCollection()
        ->and($collection)->not->toBeEmpty();
});

it('can get all', function () {
    $collection = ApuHoliday::getAll();

    expect($collection)->toBeCollection()
        ->and($collection)->not->toBeEmpty();
});
