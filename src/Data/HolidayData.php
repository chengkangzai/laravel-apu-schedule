<?php

namespace Chengkangzai\ApuSchedule\Data;

use Illuminate\Support\Carbon;
use Spatie\LaravelData\Data;

class HolidayData extends Data
{
    public function __construct(
        public int $id,
        public string $holiday_name,
        public string $holiday_description,
        public Carbon $holiday_start_date,
        public Carbon $holiday_end_date,
        public string $holiday_people_affected,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'],
            holiday_name: $data['holiday_name'],
            holiday_description: $data['holiday_description'],
            holiday_start_date: Carbon::parse($data['holiday_start_date']),
            holiday_end_date: Carbon::parse($data['holiday_end_date']),
            holiday_people_affected: $data['holiday_people_affected'],
        );
    }
}
