<?php

declare(strict_types=1);

namespace Gwo\AppsRecruitmentTask\Domain\Enum;

enum DocumentNameEnum: string
{
    case LECTURE = 'Lecture';
    case LECTURE_ENROLLMENT = 'LectureEnrollment';
    case USER = 'User';
}
