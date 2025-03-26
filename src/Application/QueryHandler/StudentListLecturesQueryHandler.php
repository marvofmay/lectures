<?php

declare(strict_types=1);

namespace Gwo\AppsRecruitmentTask\Application\QueryHandler;

use Gwo\AppsRecruitmentTask\Application\Query\StudentListLecturesQuery;
use Gwo\AppsRecruitmentTask\Application\Transformer\LectureTransformer;
use Gwo\AppsRecruitmentTask\Domain\Interface\Lecture\LectureReaderInterface;

final class StudentListLecturesQueryHandler
{
    public function __construct(private readonly LectureReaderInterface $lectureReaderRepository)
    {
    }

    public function __invoke(StudentListLecturesQuery $query): array
    {
        return $this->handle($query->getStudentUUID());
    }

    private function handle(string $studentUUID): array
    {
        $lectures = $this->lectureReaderRepository->findLecturesByStudentUUID($studentUUID);

        return LectureTransformer::transformCollection($lectures);
    }
}
