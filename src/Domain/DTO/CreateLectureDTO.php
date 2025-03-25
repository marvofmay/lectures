<?php

declare(strict_types=1);

namespace Gwo\AppsRecruitmentTask\Domain\DTO;

final readonly class CreateLectureDTO
{
    public function __construct(
        public string $name,
        public int $studentLimit,
        public \DateTimeImmutable $startDate,
        public \DateTimeImmutable $endDate
    ) {
    }
}