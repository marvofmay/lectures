<?php

namespace Gwo\AppsRecruitmentTask\Domain\DTO;

use Symfony\Component\HttpFoundation\JsonResponse;

class DeleteStudentFromLectureDTOFactory extends AbstractDTOFactory
{
    public function createFromRequest(string $lectureUUID, string $studentUUID): JsonResponse|DeleteStudentFromLectureDTO
    {
        return $this->validateDto(new DeleteStudentFromLectureDTO($lectureUUID, $studentUUID));
    }
}
