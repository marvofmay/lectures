<?php

declare(strict_types=1);

namespace Gwo\AppsRecruitmentTask\Domain\Interface\Lecture;

use Doctrine\Common\Collections\Collection;
use Gwo\AppsRecruitmentTask\Domain\Document\Lecture\Lecture;

interface LectureReaderInterface
{
    public function findByUUID(string $uuid): ?Lecture;
    public function findLecturesByStudentUUID(string $studentUUID): Collection;
}