<?php

declare(strict_types=1);

namespace Gwo\AppsRecruitmentTask\Infrastructure\Persistence\MongoDB\Writer;

use Gwo\AppsRecruitmentTask\Domain\Document\Lecture\Lecture;
use Gwo\AppsRecruitmentTask\Domain\Enum\CollectionNameEnum;
use Gwo\AppsRecruitmentTask\Domain\Enum\LectureCollectionColumnEnum;
use Gwo\AppsRecruitmentTask\Domain\Interface\Lecture\LectureWriterInterface;
use Gwo\AppsRecruitmentTask\Infrastructure\Persistence\MongoDB\DatabaseClient;

class MongoLectureWriterRepository implements LectureWriterInterface
{
    public function __construct(
        private DatabaseClient $databaseClient,
    ) {
    }

    public function saveInDB(Lecture $lecture): void
    {
        $this->databaseClient->upsert(
            CollectionNameEnum::LECTURE->value,
            [LectureCollectionColumnEnum::ID->value => (string) $lecture->getId()],
            [
                '$set' => [
                    LectureCollectionColumnEnum::ID->value => (string) $lecture->getId(),
                    LectureCollectionColumnEnum::LECTURER_ID->value => (string) $lecture->getLecturerId(),
                    LectureCollectionColumnEnum::NAME->value => $lecture->getName(),
                    LectureCollectionColumnEnum::STUDENT_LIMIT->value => $lecture->getStudentLimit(),
                    LectureCollectionColumnEnum::START_DATE->value => $lecture->getStartDate()->format('Y-m-d H:i'),
                    LectureCollectionColumnEnum::END_DATE->value => $lecture->getEndDate()->format('Y-m-d H:i'),
                ],
            ]
        );
    }
}
