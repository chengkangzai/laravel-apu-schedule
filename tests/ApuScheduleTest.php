<?php

use Chengkangzai\ApuSchedule\ApuSchedule;
use Chengkangzai\ApuSchedule\Data\ScheduleData;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Spatie\LaravelData\DataCollection;

beforeEach(function () {
    // Clear cache before each test to ensure clean state
    Cache::forget('apu_schedule_data');

    // Read the sample data from schedule.json
    $sampleData = file_get_contents(__DIR__ . '/../sample_requests/schedule.json');

    // Mock the HTTP response with sample data
    Http::fake([
        ApuSchedule::BASE_URL => Http::response(
            json_decode($sampleData, true),
            200
        )
    ]);
});

it('can fetch schedule data from S3 bucket', function () {
    $collection = ApuSchedule::get();

    expect($collection)->toBeCollection()
        ->and($collection)->not->toBeEmpty()
        ->and($collection->first())->toBeInstanceOf(ScheduleData::class);
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

    expect($collection)->toBeInstanceOf(DataCollection::class)
        ->and($collection)->not->toBeEmpty()
        ->and($collection->first())->toBeInstanceOf(ScheduleData::class)
        ->and($collection->first()->INTAKE)->toBe($intake);
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

    expect($collection)->toBeInstanceOf(DataCollection::class)
        ->and($collection)->not->toBeEmpty()
        ->and($collection->first())->toBeInstanceOf(ScheduleData::class)
        ->and($collection->first()->INTAKE)->toBe($intake)
        ->and($collection->first()->GROUPING)->toBe($grouping);
});

it('can filter timetable by excluding specific module IDs', function () {
    $intake = ApuSchedule::getIntakes()->random();
    $grouping = ApuSchedule::getGroupings($intake)->random();
    $schedule = ApuSchedule::getSchedule($intake, $grouping);

    // Only run this test if we have at least one MODID
    if ($schedule->count() <= 0) {
        $this->markTestSkipped('No schedule data available for testing filter');
    }

    $MODID = $schedule->first()->MODID;
    $scheduleWFilter = ApuSchedule::getSchedule($intake, $grouping, [$MODID]);

    expect($scheduleWFilter)->toBeInstanceOf(DataCollection::class)
        ->and($scheduleWFilter->toCollection()->pluck('MODID'))->not->toContain($MODID);

    $scheduleWOFilter = ApuSchedule::getSchedule($intake, $grouping);
    expect($scheduleWOFilter)->toBeInstanceOf(DataCollection::class)
        ->and($scheduleWFilter->count())->toBeLessThanOrEqual($scheduleWOFilter->count());
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
    // First call should make an HTTP request
    $firstResult = ApuSchedule::get();

    // Verify the request was made
    Http::assertSent(function ($request) {
        return $request->url() === ApuSchedule::BASE_URL;
    });

    // Reset the fake with different data to ensure we're using the cache
    Http::fake([
        ApuSchedule::BASE_URL => Http::response([
            ['INTAKE' => 'FAKE_INTAKE', 'GROUPING' => 'FAKE_GROUP', 'MODID' => 'FAKE_MOD']
        ], 200)
    ]);

    // Second call should use cache, not the new fake data
    $secondResult = ApuSchedule::get();

    // Verify no new HTTP request was made
    Http::assertNothingSent();

    // Results should contain the same data (may not be exactly equal due to DTO conversion)
    expect($secondResult->count())->toBe($firstResult->count());
    expect($secondResult->first()->INTAKE)->toBe($firstResult->first()->INTAKE);
});
