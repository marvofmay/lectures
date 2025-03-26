<?php

declare(strict_types=1);

namespace Gwo\AppsRecruitmentTask\Domain\Enum;

enum PermissionLectureEnum: string
{
    case CREATE = 'create';
    case ENROLL = 'enroll';
    case LIST_LECTURES = 'listLectures';
    case DELETE_STUDENT = 'deleteStudent';
}
