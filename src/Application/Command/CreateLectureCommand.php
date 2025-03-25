<?php

declare(strict_types=1);

namespace Gwo\AppsRecruitmentTask\Application\Command;

use Gwo\AppsRecruitmentTask\Domain\DTO\CreateLectureDTO;

readonly class CreateLectureCommand
{
        private string $name;
        private int $studentLimit;
        private string $startDate;
        private string $endDate;
    public function __construct(CreateLectureDTO $createLectureDTO) {
        $this->name = $createLectureDTO->name;
        $this->studentLimit = $createLectureDTO->studentLimit;
        $this->startDate = $createLectureDTO->startDate;
        $this->endDate = $createLectureDTO->endDate;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getStudentLimit(): int
    {
        return $this->studentLimit;
    }

    public function getStartDate(): string
    {
        return $this->startDate;
    }

    public function getEndDate(): string
    {
        return $this->endDate;
    }
}
