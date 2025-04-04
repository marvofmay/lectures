<?php

declare(strict_types=1);

namespace Gwo\AppsRecruitmentTask\Domain\Enum;

enum UserDocumentFieldEnum: string
{
    case ID = '_id';
    case NAME = 'name';
    case PASSWORD = 'password';
    case ROLE = 'role';
}
