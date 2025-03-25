<?php

declare(strict_types=1);

namespace Gwo\AppsRecruitmentTask\Domain\Interface\LectureEnrollment;

use Gwo\AppsRecruitmentTask\Domain\Document\LectureEnrollment\LectureEnrollment;

interface LectureEnrollmentWriterInterface
{
    public function saveInDB(LectureEnrollment $lectureEnrollment): void;
}