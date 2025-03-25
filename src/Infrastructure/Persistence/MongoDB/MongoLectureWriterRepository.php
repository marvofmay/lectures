<?php

declare(strict_types=1);

namespace Gwo\AppsRecruitmentTask\Infrastructure\Persistence\MongoDB;

use Gwo\AppsRecruitmentTask\Domain\Document\Lecture\Lecture;
use Gwo\AppsRecruitmentTask\Domain\Enum\CollectionNameEnum;
use Gwo\AppsRecruitmentTask\Domain\Interface\LectureWriterInterface;

class MongoLectureWriterRepository implements LectureWriterInterface
{
    public function __construct(
        private DatabaseClient $databaseClient
    ) {}

    public function saveInDB(Lecture $lecture): void
    {
        $this->databaseClient->upsert(
            CollectionNameEnum::LECTURE->value,
            ['_id' => (string) $lecture->getId()],
            [
                '$set' => [
                    '_id' => (string) $lecture->getId(),
                    'title' => $lecture->getName(),
                    'studentLimit' => $lecture->getStudentLimit(),
                    'startDate' => $lecture->getStartDate()->format('Y-m-d H:i:s'),
                    'endDate' => $lecture->getEndDate()->format('Y-m-d H:i:s'),
                ],
            ]
        );
    }
}