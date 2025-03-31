<?php

namespace Gwo\AppsRecruitmentTask\Domain\Exception;

use Symfony\Component\HttpFoundation\Response;

class NotFoundLectureEnrollmentException extends \Exception
{
    public function __construct(string $message = 'Record not found', ?\Throwable $previous = null)
    {
        parent::__construct($message, Response::HTTP_NOT_FOUND, $previous);
    }
}
