<?php

declare(strict_types=1);

namespace Gwo\AppsRecruitmentTask\Domain\Document\Lecture;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Gwo\AppsRecruitmentTask\Domain\Enum\CollectionNameEnum;
use Gwo\AppsRecruitmentTask\Util\StringId;

#[ODM\Document(collection: CollectionNameEnum::LECTURE->value)]
final readonly class Lecture
{
    #[ODM\Id(strategy: 'AUTO')]
    private StringId $id;

    #[ODM\Field(type: 'string', nullable: false)]
    private StringId $lecturerId;

    #[ODM\Field(type: 'string', nullable: false)]
    private string $name;

    #[ODM\Field(type: 'int', nullable: false)]
    private int $studentLimit;

    #[ODM\Field(type: 'date', nullable: false)]
    private \DateTimeImmutable $startDate;

    #[ODM\Field(type: 'date', nullable: false)]
    private \DateTimeImmutable $endDate;

    public function __construct(
        StringId $id,
        StringId $lecturerId,
        string $name,
        int $studentLimit,
        \DateTimeImmutable $startDate,
        \DateTimeImmutable $endDate,
    ) {
        $this->id = $id;
        $this->lecturerId = $lecturerId;
        $this->name = $name;
        $this->studentLimit = $studentLimit;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function getId(): StringId
    {
        return $this->id;
    }

    public function getLecturerId(): StringId
    {
        return $this->lecturerId;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getStudentLimit(): int
    {
        return $this->studentLimit;
    }

    public function getStartDate(): \DateTimeImmutable
    {
        return $this->startDate;
    }

    public function getEndDate(): \DateTimeImmutable
    {
        return $this->endDate;
    }
}
