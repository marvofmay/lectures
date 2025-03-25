<?php

declare(strict_types=1);

namespace Gwo\AppsRecruitmentTask\Infrastructure\Persistence\MongoDB\Writer;

use Gwo\AppsRecruitmentTask\Domain\Document\Lecture\Lecture;
use Gwo\AppsRecruitmentTask\Domain\Enum\CollectionNameEnum;
use Gwo\AppsRecruitmentTask\Domain\Interface\Lecture\LectureWriterInterface;
use Gwo\AppsRecruitmentTask\Infrastructure\Persistence\MongoDB\DatabaseClient;

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
                    'lecturerId' => (string) $lecture->getLecturerId(),
                    'name' => $lecture->getName(),
                    'studentLimit' => $lecture->getStudentLimit(),
                    'startDate' => $lecture->getStartDate()->format('Y-m-d H:i:s'),
                    'endDate' => $lecture->getEndDate()->format('Y-m-d H:i:s'),
                ],
            ]
        );
    }
}