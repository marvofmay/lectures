<?php

declare(strict_types=1);

namespace Gwo\AppsRecruitmentTask\Infrastructure\Persistence\MongoDB\Reader;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Gwo\AppsRecruitmentTask\Domain\Document\Lecture\Lecture;
use Gwo\AppsRecruitmentTask\Domain\Enum\CollectionNameEnum;
use Gwo\AppsRecruitmentTask\Domain\Enum\LectureCollectionColumnEnum;
use Gwo\AppsRecruitmentTask\Domain\Enum\LectureEnrollmentCollectionColumnEnum;
use Gwo\AppsRecruitmentTask\Domain\Enum\UserCollectionColumnEnum;
use Gwo\AppsRecruitmentTask\Domain\Interface\Lecture\LectureReaderInterface;
use Gwo\AppsRecruitmentTask\Infrastructure\Persistence\MongoDB\DatabaseClient;
use Gwo\AppsRecruitmentTask\Util\StringId;

class MongoLectureReaderRepository implements LectureReaderInterface
{
    public function __construct(
        private DatabaseClient $databaseClient,
    ) {
    }

    public function findByUUID(string $uuid): ?Lecture
    {
        $result = $this->databaseClient->getByQuery(CollectionNameEnum::LECTURE->value, [LectureCollectionColumnEnum::ID->value => $uuid]);

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

    public function findLecturesByStudentUUID(string $studentUUID): Collection
    {
        $lectureEnrollments = $this->databaseClient->getByQuery(
            CollectionNameEnum::LECTURE_ENROLLMENT->value,
            [LectureEnrollmentCollectionColumnEnum::STUDENT_ID->value => $studentUUID]
        );

        if (empty($lectureEnrollments)) {
            return new ArrayCollection();
        }

        $lectureIds = array_map(fn ($enrollment) => $enrollment['lectureId'], $lectureEnrollments);

        $lectures = $this->databaseClient->getByQuery(
            CollectionNameEnum::LECTURE->value,
            [LectureCollectionColumnEnum::ID->value => ['$in' => $lectureIds]]
        );

        if (empty($lectures)) {
            return new ArrayCollection();
        }

        $lecturerIds = array_unique(array_map(fn ($lecture) => $lecture['lecturerId'], $lectures));
        $lecturers = $this->databaseClient->getByQuery(
            CollectionNameEnum::USER->value,
            [UserCollectionColumnEnum::ID->value => ['$in' => $lecturerIds]]
        );

        $lecturerMap = [];
        foreach ($lecturers as $lecturer) {
            $lecturerMap[$lecturer['_id']] = $lecturer['name'];
        }

        $lectureObjects = array_map(
            fn ($lectureData) => [
                'id' => (string) $lectureData['_id'],
                'name' => $lectureData['name'],
                'studentLimit' => $lectureData['studentLimit'],
                'startDate' => (new \DateTimeImmutable($lectureData['startDate'])),
                'endDate' => (new \DateTimeImmutable($lectureData['endDate'])),
                'lecturerName' => $lecturerMap[$lectureData['lecturerId']] ?? 'Nieznany wykładowca',
            ],
            $lectures
        );

        return new ArrayCollection($lectureObjects);
    }
}
