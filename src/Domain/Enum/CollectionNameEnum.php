<?php

declare(strict_types=1);

namespace Gwo\AppsRecruitmentTask\Domain\Enum;

enum CollectionNameEnum: string
{
    case LECTURE            = 'Lecture';
    case LECTURE_ENROLLMENT = 'LectureEnrollment';
    case USER               = 'User';
}