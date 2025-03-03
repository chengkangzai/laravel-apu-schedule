<?php

use Chengkangzai\ApuSchedule\ApuSchedule;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

it('can fetch schedule data from S3 bucket', function () {
    $collection = ApuSchedule::get();

    expect($collection)->toBeCollection()
        ->and($collection)->not->toBeEmpty();
});

it('can retrieve available intakes from schedule data', function () {
    $collection = ApuSchedule::getIntakes();

    expect($collection)->toBeCollection()
        ->and($collection)->not->toBeEmpty();
});

it('can retrieve groupings for a specific intake', function () {
    $intake = ApuSchedule::getIntakes()->random();
    $collection = ApuSchedule::getGroupings($intake);

    expect($collection)->toBeCollection()
        ->and($collection)->not->toBeEmpty();
});

it('can retrieve schedule data filtered by intake', function () {
    $intake = ApuSchedule::getIntakes()->random();
    $collection = ApuSchedule::getByIntake($intake);

    expect($collection)->toBeCollection()
        ->and($collection)->not->toBeEmpty();
});

it('can retrieve module IDs for a specific intake and grouping', function () {
    $intake = ApuSchedule::getIntakes()->random();
    $grouping = ApuSchedule::getGroupings($intake)->random();
    $collection = ApuSchedule::getMODID($intake, $grouping);

    expect($collection)->toBeCollection()
        ->and($collection)->not->toBeEmpty();
});

it('can retrieve timetable based on intake and grouping', function () {
    $intake = ApuSchedule::getIntakes()->random();
    $grouping = ApuSchedule::getGroupings($intake)->random();
    $collection = ApuSchedule::getSchedule($intake, $grouping);

    expect($collection)->toBeCollection()
        ->and($collection)->not->toBeEmpty();
});

it('can filter timetable by excluding specific module IDs', function () {
    $intake = ApuSchedule::getIntakes()->random();
    $grouping = ApuSchedule::getGroupings($intake)->random();
    $MODID = ApuSchedule::getSchedule($intake, $grouping)->random()['MODID'];
    $scheduleWFilter = ApuSchedule::getSchedule($intake, $grouping, [$MODID]);

    expect($scheduleWFilter)->toBeCollection()
        ->and($scheduleWFilter)->not->toBeEmpty()
        ->and($scheduleWFilter->pluck('MODID'))->not->toContain($MODID);

    $scheduleWOFilter = ApuSchedule::getSchedule($intake, $grouping);
    expect($scheduleWOFilter)->toBeCollection()
        ->and($scheduleWOFilter)->not->toBeEmpty()
        ->and($scheduleWFilter->count())->toBeLessThan($scheduleWOFilter->count());
});

it('can successfully clear the schedule cache', function () {
    // First, make sure cache has data
    ApuSchedule::get();

    // Verify cache exists
    expect(Cache::has('apu_schedule_data'))->toBeTrue();

    // Clear the cache
    $result = ApuSchedule::clearCache();

    // Verify the cache was cleared
    expect($result)->toBeTrue()
        ->and(Cache::has('apu_schedule_data'))->toBeFalse();
});

it('caches schedule data to avoid unnecessary S3 requests', function () {
    // Mock the HTTP call
    Http::fake([
        ApuSchedule::BASE_URL => Http::response([
            ['INTAKE' => 'UC1F1801CS', 'GROUPING' => 'G1', 'MODID' => 'CT001']
        ], 200)
    ]);

    // Clear any existing cache
    Cache::forget('apu_schedule_data');

    // First call should make an HTTP request
    $firstResult = ApuSchedule::get();

    // Verify the request was made
    Http::assertSent(function ($request) {
        return $request->url() === ApuSchedule::BASE_URL;
    });

    // Reset the request count
    Http::fake([
        ApuSchedule::BASE_URL => Http::response([
            ['INTAKE' => 'UC1F1801CS', 'GROUPING' => 'G1', 'MODID' => 'CT001']
        ], 200)
    ]);

    // Second call should use cache
    $secondResult = ApuSchedule::get();

    // Verify no new HTTP request was made
    Http::assertNothingSent();

    // Results should be the same
    expect($secondResult)->toEqual($firstResult);
});
