<?php

namespace Gwo\AppsRecruitmentTask\Domain\DTO;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class DeleteStudentFromLectureDTOFactory
{
    public function __construct(private ValidatorInterface $validator)
    {
    }

    public function createFromRequest(string $lectureUUID, string $studentUUID): JsonResponse|DeleteStudentFromLectureDTO
    {
        $dto = new DeleteStudentFromLectureDTO($lectureUUID, $studentUUID);

        $errors = $this->validator->validate($dto);
        if (count($errors) > 0) {
            $errorMessages = [];
            foreach ($errors as $error) {
                $errorMessages[] = $error->getPropertyPath().': '.$error->getMessage();
            }

            return new JsonResponse(['errors' => $errorMessages], Response::HTTP_BAD_REQUEST);
        }

        return $dto;
    }
}
