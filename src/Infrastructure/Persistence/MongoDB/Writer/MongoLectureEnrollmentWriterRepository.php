<?php

declare(strict_types=1);

namespace Gwo\AppsRecruitmentTask\Infrastructure\Persistence\MongoDB\Writer;

use Gwo\AppsRecruitmentTask\Domain\Document\LectureEnrollment\LectureEnrollment;
use Gwo\AppsRecruitmentTask\Domain\Enum\CollectionNameEnum;
use Gwo\AppsRecruitmentTask\Domain\Interface\LectureEnrollment\LectureEnrollmentWriterInterface;
use Gwo\AppsRecruitmentTask\Infrastructure\Persistence\MongoDB\DatabaseClient;

class MongoLectureEnrollmentWriterRepository implements LectureEnrollmentWriterInterface
{
    public function __construct(
        private DatabaseClient $databaseClient
    ) {}

    public function saveInDB(LectureEnrollment $lectureEnrollment): void
    {
        $this->databaseClient->upsert(
            CollectionNameEnum::LECTURE_ENROLLMENT->value,
            ['_id' => (string) $lectureEnrollment->getId()],
            [
                '$set' => [
                    '_id' => (string) $lectureEnrollment->getId(),
                    'lectureId' => (string) $lectureEnrollment->getLectureId(),
                    'studentId' => (string) $lectureEnrollment->getStudentId(),
                ],
            ]
        );
    }

    public function deleteInDB(LectureEnrollment $lectureEnrollment): void
    {
        $this->databaseClient->deleteDocuments(CollectionNameEnum::LECTURE_ENROLLMENT->value, [
            'lectureId' => (string) $lectureEnrollment->getLectureId(),
            'studentId' => (string) $lectureEnrollment->getStudentId(),
        ]);
    }
}