<?php

declare(strict_types=1);

namespace Gwo\AppsRecruitmentTask\Domain\Enum;

enum UserRole: string
{
    case LECTURER = 'lecturer';
    case STUDENT = 'student';
}
