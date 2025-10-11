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
            self::CurriculumVitae => 'heroicon-o-document-text',
            self::CoverLetter => 'heroicon-o-envelope',
            self::LetterOfInterest => 'heroicon-o-heart',
            self::FollowUpLetter => 'heroicon-o-arrow-path',
            self::AcceptanceLetter => 'heroicon-o-check-circle',
            self::DeclineLetter => 'heroicon-o-x-circle',
            self::Other => 'heroicon-o-document',
        };
    }
}
