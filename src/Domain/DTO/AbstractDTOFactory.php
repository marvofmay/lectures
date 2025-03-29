<?php

declare(strict_types=1);

namespace Gwo\AppsRecruitmentTask\Domain\DTO;

use Gwo\AppsRecruitmentTask\Domain\Interface\DTOInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class AbstractDTOFactory
{
    public function __construct(protected ValidatorInterface $validator)
    {
    }

    protected function validateDto(DTOInterface $dto): JsonResponse|DTOInterface
    {
        $errors = $this->validator->validate($dto);
        if (count($errors) > 0) {
            $errorMessages = array_map(fn($error) => $error->getPropertyPath() . ': ' . $error->getMessage(), iterator_to_array($errors));
            return new JsonResponse(['errors' => $errorMessages], Response::HTTP_BAD_REQUEST);
        }

        return $dto;
    }
}