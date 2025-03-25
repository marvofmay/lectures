<?php

declare(strict_types=1);

namespace Gwo\AppsRecruitmentTask\Infrastructure\Persistence\MongoDB\Reader;

use Gwo\AppsRecruitmentTask\Domain\Enum\CollectionNameEnum;
use Gwo\AppsRecruitmentTask\Domain\Interface\LectureEnrollment\LectureEnrollmentReaderInterface;
use Gwo\AppsRecruitmentTask\Infrastructure\Persistence\MongoDB\DatabaseClient;

class MongoLectureEnrollmentReaderRepository implements LectureEnrollmentReaderInterface
{
    public function __construct(
        private DatabaseClient $databaseClient
    ) {}

    public function countEnrolledStudentsByLectureId(string $lectureId): int
    {
        return $this->databaseClient->countDocuments(CollectionNameEnum::LECTURE_ENROLLMENT->value, ['lectureId' => $lectureId]);
    }

    public function isStudentAlreadyEnrolled(string $lectureId, string $studentId): bool
    {
        $result = $this->databaseClient->getByQuery(CollectionNameEnum::LECTURE_ENROLLMENT->value, [
            'lectureId' => $lectureId,
            'studentId' => $studentId
        ]);

        return !empty($result);
    }
}