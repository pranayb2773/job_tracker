<?php

declare(strict_types=1);

namespace App\Enums;

enum ApplicationStatus: string
{
    case Applied = 'applied';
    case Screening = 'screening';
    case Interview = 'interview';
    case TechnicalTest = 'technical_test';
    case FinalInterview = 'final_interview';
    case Offer = 'offer';
    case Accepted = 'accepted';
    case Rejected = 'rejected';
    case Withdrawn = 'withdrawn';

    public function getLabel(): string
    {
        return match ($this) {
            self::Applied => 'Application',
            self::Screening => 'Screening',
            self::Interview => 'Interviewing',
            self::TechnicalTest => 'Technical Test',
            self::FinalInterview => 'Final Interview',
            self::Offer => 'Offer',
            self::Accepted => 'Accepted',
            self::Rejected => 'Rejected',
            self::Withdrawn => 'Withdrawn',
        };
    }

    public function getOrder(): int
    {
        return match ($this) {
            self::Applied => 1,
            self::Screening => 2,
            self::Interview => 3,
            self::TechnicalTest => 4,
            self::FinalInterview => 5,
            self::Offer => 6,
            self::Accepted => 7,
            self::Rejected => 8,
            self::Withdrawn => 9,
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::Applied => 'violet',
            self::Screening => 'blue',
            self::Interview => 'amber',
            self::TechnicalTest => 'yellow',
            self::FinalInterview => 'pint',
            self::Offer => 'green',
            self::Accepted => 'emerald',
            self::Rejected => 'red',
            self::Withdrawn => 'rose',
        };
    }
}
