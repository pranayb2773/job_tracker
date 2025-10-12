<?php

declare(strict_types=1);

namespace App\Enums;

enum DocumentType: string
{
    case CurriculumVitae = 'curriculum_vitae';
    case CoverLetter = 'cover_letter';
    case LetterOfInterest = 'letter_of_interest';
    case FollowUpLetter = 'follow_up_letter';
    case AcceptanceLetter = 'acceptance_letter';
    case DeclineLetter = 'decline_letter';
    case Other = 'other';

    public function getLabel(): string
    {
        return match ($this) {
            self::CurriculumVitae => __('Curriculum Vitae'),
            self::CoverLetter => __('Cover Letter'),
            self::LetterOfInterest => __('Letter of Interest'),
            self::FollowUpLetter => __('Follow Up Letter'),
            self::AcceptanceLetter => __('Accept Letter'),
            self::DeclineLetter => __('Decline Letter'),
            self::Other => __('Other'),
        };
    }

    public function getIcon(): string
    {
        return match ($this) {
            self::CurriculumVitae => 'document-text',
            self::CoverLetter => 'envelope',
            self::LetterOfInterest => 'heart',
            self::FollowUpLetter => 'arrow-path',
            self::AcceptanceLetter => 'check-circle',
            self::DeclineLetter => 'x-circle',
            self::Other => 'document',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::CurriculumVitae => 'blue',
            self::CoverLetter => 'green',
            self::LetterOfInterest => 'pink',
            self::FollowUpLetter => 'purple',
            self::AcceptanceLetter => 'lime',
            self::DeclineLetter => 'red',
            self::Other => 'zinc',
        };
    }
}
