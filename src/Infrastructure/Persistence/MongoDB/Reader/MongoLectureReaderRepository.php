<?php

declare(strict_types=1);

namespace Gwo\AppsRecruitmentTask\Infrastructure\Persistence\MongoDB\Reader;

use Gwo\AppsRecruitmentTask\Domain\Document\Lecture\Lecture;
use Gwo\AppsRecruitmentTask\Domain\Enum\CollectionNameEnum;
use Gwo\AppsRecruitmentTask\Domain\Interface\Lecture\LectureReaderInterface;
use Gwo\AppsRecruitmentTask\Infrastructure\Persistence\MongoDB\DatabaseClient;
use Gwo\AppsRecruitmentTask\Util\StringId;

class MongoLectureReaderRepository implements LectureReaderInterface
{
    public function __construct(
        private DatabaseClient $databaseClient
    ) {}

    public function findByUUID(string $uuid): ?Lecture
    {
        $result = $this->databaseClient->getByQuery(CollectionNameEnum::LECTURE->value, ['_id' => $uuid]);

        if (empty($result)) {
            return null;
        }

        $lectureData = $result[0];

        return new Lecture(
            new StringId($lectureData['_id']),
            new StringId($lectureData['lecturerId']),
            $lectureData['name'],
            $lectureData['studentLimit'],
            new \DateTimeImmutable($lectureData['startDate']),
            new \DateTimeImmutable($lectureData['endDate'])
        );
    }
}