<?php

use Chengkangzai\ApuSchedule\ApuHoliday;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

it('can get raw holiday data from API', function () {
    $collection = ApuHoliday::getRaw();

    expect($collection)->toBeCollection()
        ->and($collection)->not->toBeEmpty();
});

it('can retrieve holidays filtered by specific year', function () {
    $raw = ApuHoliday::getRaw();
    //year is not unique, so we need to get the first one
    $year = $raw->first()['year'];
    $collection = ApuHoliday::getByYear($year);

    expect($collection)->toBeCollection()
        ->and($collection)->not->toBeEmpty();
});

it('can retrieve all holidays from all years', function () {
    $collection = ApuHoliday::getAll();

    expect($collection)->toBeCollection()
        ->and($collection)->not->toBeEmpty();
});

it('can successfully clear the holiday cache', function () {
    // First, make sure cache has data
    ApuHoliday::getRaw();

    // Verify cache exists
    expect(Cache::has('apu_holidays_data'))->toBeTrue();

    // Clear the cache
    $result = ApuHoliday::clearCache();

    // Verify the cache was cleared
    expect($result)->toBeTrue()
        ->and(Cache::has('apu_holidays_data'))->toBeFalse();
});

it('caches holiday data to avoid unnecessary API calls', function () {
    // Mock the HTTP call
    Http::fake([
        ApuHoliday::BASE_URL => Http::response([
            ['year' => 2023, 'holidays' => [['name' => 'Test Holiday']]]
        ], 200)
    ]);

    // Clear any existing cache
    Cache::forget('apu_holidays_data');

    // First call should make an HTTP request
    $firstResult = ApuHoliday::getRaw();

    // Verify the request was made
    Http::assertSent(function ($request) {
        return $request->url() === ApuHoliday::BASE_URL;
    });

    // Reset the request count
    Http::fake([
        ApuHoliday::BASE_URL => Http::response([
            ['year' => 2023, 'holidays' => [['name' => 'Test Holiday']]]
        ], 200)
    ]);

    // Second call should use cache
    $secondResult = ApuHoliday::getRaw();

    // Verify no new HTTP request was made
    Http::assertNothingSent();

    // Results should be the same
    expect($secondResult)->toEqual($firstResult);
});
