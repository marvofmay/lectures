<?php

declare(strict_types=1);

namespace Gwo\AppsRecruitmentTask\Domain\Enum;

enum LectureCollectionColumnEnum: string
{
    case ID = '_id';
    case LECTURER_ID = 'lecturerId';
    case NAME = 'name';
    case STUDENT_LIMIT = 'studentLimit';
    case START_DATE = 'startDate';
    case END_DATE = 'endDate';
}
