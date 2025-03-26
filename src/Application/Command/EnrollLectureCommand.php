<?php

declare(strict_types=1);

namespace Gwo\AppsRecruitmentTask\Application\Command;

use Gwo\AppsRecruitmentTask\Domain\DTO\EnrollLectureDTO;

readonly class EnrollLectureCommand
{
    private string $lectureUUID;
    private string $studentUUID;

    public function __construct(EnrollLectureDTO $enrollLectureDTO, string $studentUUID)
    {
        $this->lectureUUID = $enrollLectureDTO->lectureUUID;
        $this->studentUUID = $studentUUID;
    }

    public function getLectureUUID(): string
    {
        return $this->lectureUUID;
    }

    public function getStudentUUID(): string
    {
        return $this->studentUUID;
    }
}
