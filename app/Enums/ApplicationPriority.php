<?php

namespace App\Enums;

enum ApplicationPriority : string
{
    case VeryHigh = 'very_high';
    case High = 'high';
    case Medium = 'medium';
    case Low = 'low';
    case VeryLow = 'very_low';

    public function getLabel(): string
    {
        return match ($this) {
            self::VeryHigh => __('Very High'),
            self::High => __('High'),
            self::Medium => __('Medium'),
            self::Low => __('Low'),
            self::VeryLow => __('Very Low'),
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::VeryHigh => 'red',
            self::High => 'rose',
            self::Medium => 'blue',
            self::Low => 'yellow',
            self::VeryLow => 'zinc',
        };
    }
}
