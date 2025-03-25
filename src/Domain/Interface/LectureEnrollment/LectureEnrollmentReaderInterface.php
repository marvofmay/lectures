<?php

declare(strict_types=1);

namespace Gwo\AppsRecruitmentTask\Domain\Interface\LectureEnrollment;

interface LectureEnrollmentReaderInterface
{
    public function countEnrolledStudentsByLectureId(string $lectureId): int;
}