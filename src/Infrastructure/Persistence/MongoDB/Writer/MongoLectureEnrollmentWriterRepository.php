<?php

declare(strict_types=1);

namespace Gwo\AppsRecruitmentTask\Infrastructure\Persistence\MongoDB\Writer;

use Gwo\AppsRecruitmentTask\Domain\Document\LectureEnrollment\LectureEnrollment;
use Gwo\AppsRecruitmentTask\Domain\Enum\CollectionNameEnum;
use Gwo\AppsRecruitmentTask\Domain\Enum\LectureEnrollmentDocumentFieldEnum;
use Gwo\AppsRecruitmentTask\Domain\Interface\LectureEnrollment\LectureEnrollmentWriterInterface;
use Gwo\AppsRecruitmentTask\Infrastructure\Persistence\MongoDB\DatabaseClient;

class MongoLectureEnrollmentWriterRepository implements LectureEnrollmentWriterInterface
{
    public function __construct(
        private DatabaseClient $databaseClient,
    ) {
    }

    public function saveInDB(LectureEnrollment $lectureEnrollment): void
    {
        $this->databaseClient->upsert(
            CollectionNameEnum::LECTURE_ENROLLMENT->value,
            [LectureEnrollmentDocumentFieldEnum::ID->value => (string) $lectureEnrollment->getId()],
            [
                '$set' => [
                    LectureEnrollmentDocumentFieldEnum::ID->value => (string) $lectureEnrollment->getId(),
                    LectureEnrollmentDocumentFieldEnum::LECTURE_ID->value => (string) $lectureEnrollment->getLectureId(),
                    LectureEnrollmentDocumentFieldEnum::STUDENT_ID->value => (string) $lectureEnrollment->getStudentId(),
                ],
            ]
        );
    }

    public function deleteInDB(LectureEnrollment $lectureEnrollment): void
    {
        $this->databaseClient->deleteDocuments(CollectionNameEnum::LECTURE_ENROLLMENT->value, [
            LectureEnrollmentDocumentFieldEnum::LECTURE_ID->value => (string) $lectureEnrollment->getLectureId(),
            LectureEnrollmentDocumentFieldEnum::STUDENT_ID->value => (string) $lectureEnrollment->getStudentId(),
        ]);
    }
}
