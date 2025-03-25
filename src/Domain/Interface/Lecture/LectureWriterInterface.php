<?php

declare(strict_types=1);

namespace Gwo\AppsRecruitmentTask\Domain\Interface\Lecture;

use Gwo\AppsRecruitmentTask\Domain\Document\Lecture\Lecture;

interface LectureWriterInterface
{
    public function saveInDB(Lecture $lecture): void;
}