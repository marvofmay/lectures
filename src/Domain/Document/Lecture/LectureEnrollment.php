<?php

declare(strict_types=1);

namespace Gwo\AppsRecruitmentTask\Domain\Document\Lecture;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Gwo\AppsRecruitmentTask\Util\StringId;

#[ODM\Document(collection: 'LectureEnrollment')]
final readonly class LectureEnrollment
{
    #[ODM\Id(type: 'string', strategy: 'auto')]
    private ?string $id;

    #[ODM\Field(type: "string", nullable: false)]
    private StringId $lectureId;

    #[ODM\Field(type: "string", nullable: false)]
    private StringId $studentId;

    public function __construct(
        StringId $lectureId,
        StringId $studentId
    ) {
        $this->lectureId = $lectureId;
        $this->studentId = $studentId;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getLectureId(): StringId
    {
        return $this->lectureId;
    }

    public function getStudentId(): StringId
    {
        return $this->studentId;
    }
}
