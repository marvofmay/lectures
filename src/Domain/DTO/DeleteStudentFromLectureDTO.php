<?php

declare(strict_types=1);

namespace Gwo\AppsRecruitmentTask\Domain\DTO;

use Gwo\AppsRecruitmentTask\Domain\Interface\DTOInterface;
use Symfony\Component\Validator\Constraints as Assert;

final class DeleteStudentFromLectureDTO implements DTOInterface
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Uuid]
        public string $lectureUUID,
        #[Assert\NotBlank]
        #[Assert\Uuid]
        public string $studentUUID,
    ) {
    }
}
