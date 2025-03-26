<?php

declare(strict_types=1);

namespace Gwo\AppsRecruitmentTask\Application\Query;

readonly class StudentListLecturesQuery
{
    public function __construct(private string $studentUUID)
    {
    }

    public function getStudentUUID(): string
    {
        return $this->studentUUID;
    }
}