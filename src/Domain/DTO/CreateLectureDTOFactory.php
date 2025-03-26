<?php

namespace Gwo\AppsRecruitmentTask\Domain\DTO;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class CreateLectureDTOFactory
{
    public function __construct(private ValidatorInterface $validator)
    {
    }

    public function createFromRequest(Request $request): JsonResponse|CreateLectureDTO
    {
        $data = json_decode($request->getContent(), true);

        $dto = new CreateLectureDTO(
            $data['name'] ?? '',
            $data['studentLimit'] ?? 0,
            $data['startDate'] ?? '',
            $data['endDate'] ?? '',
        );

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
