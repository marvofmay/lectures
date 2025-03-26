<?php

declare(strict_types=1);

namespace Gwo\AppsRecruitmentTask\Domain\DTO;

use Symfony\Component\Validator\Constraints as Assert;

final class EnrollLectureDTO
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Uuid]
        public string $lectureUUID,
    ) {
    }
}
