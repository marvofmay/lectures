<?php

declare(strict_types=1);

namespace Gwo\AppsRecruitmentTask\Domain\Interface;

use Gwo\AppsRecruitmentTask\Domain\Document\Lecture\Lecture;

interface LectureReaderInterface
{
    public function findByUUID(string $uuid): ?Lecture;
}