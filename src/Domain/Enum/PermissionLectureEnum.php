<?php

declare(strict_types=1);

namespace Gwo\AppsRecruitmentTask\Domain\Enum;

enum PermissionLectureEnum: string
{
    case CREATE  = 'create';
    case DELETE  = 'delete';
    case VIEW    = 'list';
}