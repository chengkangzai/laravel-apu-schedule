<?php

use Chengkangzai\ApuSchedule\ApuHoliday;
use Chengkangzai\ApuSchedule\Data\HolidayData;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Spatie\LaravelData\DataCollection;

beforeEach(function () {
    // Clear cache before each test to ensure clean state
    Cache::forget('apu_holidays_data');

    // Read the sample data from holidays.json
    $sampleData = file_get_contents(__DIR__ . '/../sample_requests/holidays.json');

    // Mock the HTTP response with sample data
    Http::fake([
        ApuHoliday::BASE_URL => Http::response(
            json_decode($sampleData, true),
            200
        ),
    ]);
});

it('can fetch raw holiday data from API', function () {
    $collection = ApuHoliday::getRaw();

    expect($collection)->toBeCollection()
        ->and($collection)->not->toBeEmpty();
});

it('can retrieve holidays filtered by specific year', function () {
    $raw = ApuHoliday::getRaw();
    //year is not unique, so we need to get the first one
    $year = $raw->first()['year'];
    $collection = ApuHoliday::getByYear($year);

    expect($collection)->toBeInstanceOf(DataCollection::class)
        ->and($collection)->not->toBeEmpty()
        ->and($collection->first())->toBeInstanceOf(HolidayData::class);
});

it('can retrieve all holidays from all years', function () {
    $collection = ApuHoliday::getAll();

    expect($collection)->toBeInstanceOf(DataCollection::class)
        ->and($collection)->not->toBeEmpty()
        ->and($collection->first())->toBeInstanceOf(HolidayData::class);
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
    // First call should make an HTTP request
    $firstResult = ApuHoliday::getRaw();

    // Verify the request was made
    Http::assertSent(function ($request) {
        return $request->url() === ApuHoliday::BASE_URL;
    });

    // Reset the fake with different data to ensure we're using the cache
    Http::fake([
        ApuHoliday::BASE_URL => Http::response([
            ['year' => 9999, 'holidays' => [['name' => 'Should not reach this']]],
        ], 200),
    ]);

    // Second call should use cache, not the new fake data
    $secondResult = ApuHoliday::getRaw();

    // Verify no new HTTP request was made
    Http::assertNothingSent();

    // Results should be the same as first call
    expect($secondResult)->toEqual($firstResult);
});
