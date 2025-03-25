<?php

declare(strict_types=1);

namespace Gwo\AppsRecruitmentTask\Application\Command;

use Gwo\AppsRecruitmentTask\Domain\DTO\EnrollLectureDTO;

readonly class EnrollLectureCommand
{
        private string $lectureUUID;
    public function __construct(EnrollLectureDTO $enrollLectureDTO) {
        $this->lectureUUID = $enrollLectureDTO->lectureUUID;
    }

    public function getLectureUUID(): string
    {
        return $this->lectureUUID;
    }
}
