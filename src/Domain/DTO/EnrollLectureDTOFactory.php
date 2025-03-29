<?php

namespace Gwo\AppsRecruitmentTask\Domain\DTO;

use Symfony\Component\HttpFoundation\JsonResponse;

class EnrollLectureDTOFactory extends AbstractDTOFactory
{
    public function createFromRequest(string $lectureUUID): JsonResponse|EnrollLectureDTO
    {
        return $this->validateDto(new EnrollLectureDTO($lectureUUID));
    }
}
