<?php

namespace Gwo\AppsRecruitmentTask\Domain\DTO;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class CreateLectureDTOFactory extends AbstractDTOFactory
{
    public function createFromRequest(Request $request): JsonResponse|CreateLectureDTO
    {
        $data = json_decode($request->getContent(), true);

        $dto = new CreateLectureDTO(
            $data['name'] ?? '',
            $data['studentLimit'] ?? 0,
            $data['startDate'] ?? '',
            $data['endDate'] ?? ''
        );

        return $this->validateDto($dto);
    }
}