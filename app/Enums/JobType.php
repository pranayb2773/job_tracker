<?php

declare(strict_types=1);

namespace App\Enums;

enum JobType: string
{
    case FullTime = 'full_time';
    case PartTime = 'part_time';
    case Freelance = 'freelance';
    case FixedTerm = 'fixed_term';
    case Contract = 'contract';

    public function getLabel(): string
    {
        return match ($this) {
            self::FullTime => __('Full Time'),
            self::PartTime => __('Part Time'),
            self::Freelance => __('Freelance'),
            self::FixedTerm => __('Fixed Term'),
            self::Contract => __('Contract'),
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::FullTime => 'blue',
            self::PartTime => 'purple',
            self::Freelance => 'orange',
            self::FixedTerm => 'green',
            self::Contract => 'cyan',
        };
    }
}
