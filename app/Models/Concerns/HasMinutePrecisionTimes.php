<?php

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Carbon;

trait HasMinutePrecisionTimes
{
    protected function start(): Attribute
    {
        return $this->minutePrecisionTime();
    }

    protected function end(): Attribute
    {
        return $this->minutePrecisionTime();
    }

    protected function sum(): Attribute
    {
        return $this->minutePrecisionTime();
    }

    protected function pause(): Attribute
    {
        return Attribute::make(
            get: fn (mixed $value) => $this->formatMinutePrecisionTime($value),
            set: fn (mixed $value) => $this->normalizePauseTime($value),
        );
    }

    protected function minutePrecisionTime(): Attribute
    {
        return Attribute::make(
            get: fn (mixed $value) => $this->formatMinutePrecisionTime($value),
            set: fn (mixed $value) => $this->normalizeMinutePrecisionTime($value),
        );
    }

    protected function formatMinutePrecisionTime(mixed $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        return Carbon::parse($value)->format('H:i');
    }

    protected function normalizeMinutePrecisionTime(mixed $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        return Carbon::parse($value)->format('H:i:s');
    }

    protected function normalizePauseTime(mixed $value): string
    {
        if ($value === null || $value === '') {
            return '00:00:00';
        }

        if (is_numeric($value)) {
            return Carbon::createFromTime(0, (int) $value)->format('H:i:s');
        }

        return Carbon::parse($value)->format('H:i:s');
    }
}