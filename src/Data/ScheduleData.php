<?php

namespace Chengkangzai\ApuSchedule\Data;

use Spatie\LaravelData\Data;

class ScheduleData extends Data
{
    public function __construct(
        public ?int $id,
        public string $INTAKE,
        public string $MODID,
        public string $MODULE_NAME,
        public string $DAY,
        public string $LOCATION,
        public string $ROOM,
        public string $LECTID,
        public string $NAME,
        public string $SAMACCOUNTNAME,
        public string $DATESTAMP,
        public string $DATESTAMP_ISO,
        public string $TIME_FROM,
        public string $TIME_TO,
        public string $TIME_FROM_ISO,
        public string $TIME_TO_ISO,
        public string $GROUPING,
        public string $CLASS_CODE,
        public string $COLOR,
    ) {
    }

    public static function fromArray(array $data): self
    {
        // Extract the key as ID if it exists (since your JSON structure seems to use keys as IDs)
        $id = isset($data['id']) ? $data['id'] : null;

        return new self(
            id: $id,
            INTAKE: $data['INTAKE'],
            MODID: $data['MODID'],
            MODULE_NAME: $data['MODULE_NAME'],
            DAY: $data['DAY'],
            LOCATION: $data['LOCATION'],
            ROOM: $data['ROOM'],
            LECTID: $data['LECTID'],
            NAME: $data['NAME'],
            SAMACCOUNTNAME: $data['SAMACCOUNTNAME'],
            DATESTAMP: $data['DATESTAMP'],
            DATESTAMP_ISO: $data['DATESTAMP_ISO'],
            TIME_FROM: $data['TIME_FROM'],
            TIME_TO: $data['TIME_TO'],
            TIME_FROM_ISO: $data['TIME_FROM_ISO'],
            TIME_TO_ISO: $data['TIME_TO_ISO'],
            GROUPING: $data['GROUPING'],
            CLASS_CODE: $data['CLASS_CODE'],
            COLOR: $data['COLOR']
        );
    }

    public function getFormattedTimeRange(): string
    {
        return "{$this->TIME_FROM} - {$this->TIME_TO}";
    }
}
