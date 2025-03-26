<?php

declare(strict_types=1);

namespace Gwo\AppsRecruitmentTask\Domain\DTO;

use Symfony\Component\Validator\Constraints as Assert;

final class CreateLectureDTO
{
    #[Assert\NotBlank]
    #[Assert\Length(min: 3, max: 255)]
    public string $name;

    #[Assert\NotBlank]
    #[Assert\Type('integer')]
    #[Assert\GreaterThan(0)]
    public int $studentLimit;

    #[Assert\NotBlank]
    #[Assert\DateTime(format: 'Y-m-d H:i')]
    public string $startDate;

    #[Assert\NotBlank]
    #[Assert\DateTime(format: 'Y-m-d H:i')]
    public string $endDate;

    public function __construct(
        string $name,
        int $studentLimit,
        string $startDate,
        string $endDate,
    ) {
        $this->name = $name;
        $this->studentLimit = $studentLimit;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }
}
