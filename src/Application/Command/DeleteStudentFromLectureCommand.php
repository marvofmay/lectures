<?php

declare(strict_types=1);

namespace Gwo\AppsRecruitmentTask\Application\Command;

use Gwo\AppsRecruitmentTask\Domain\DTO\DeleteStudentFromLectureDTO;

readonly class DeleteStudentFromLectureCommand
{
        private string $lectureUUID;
        private string $studentUUID;
    public function __construct(DeleteStudentFromLectureDTO $deleteStudentFromLectureDTO) {
        $this->lectureUUID = $deleteStudentFromLectureDTO->lectureUUID;
        $this->studentUUID = $deleteStudentFromLectureDTO->studentUUID;
    }

    public function getLectureUUID(): string
    {
        return $this->lectureUUID;
    }

    public function getStudentUUID(): string {
        return $this->studentUUID;
    }
}
