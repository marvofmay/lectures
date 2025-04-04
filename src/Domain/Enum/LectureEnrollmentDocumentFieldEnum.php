<?php

declare(strict_types=1);

namespace Gwo\AppsRecruitmentTask\Domain\Enum;

enum LectureEnrollmentDocumentFieldEnum: string
{
    case ID = '_id';
    case LECTURE_ID = 'lectureId';
    case STUDENT_ID = 'studentId';
}
