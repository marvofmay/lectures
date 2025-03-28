<?php

namespace Gwo\AppsRecruitmentTask\Domain\DTO;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class EnrollLectureDTOFactory
{
    public function __construct(private ValidatorInterface $validator)
    {
    }

    public function createFromRequest(string $lectureUUID): JsonResponse|EnrollLectureDTO
    {
        $dto = new EnrollLectureDTO($lectureUUID);

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
