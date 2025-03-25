<?php

declare(strict_types=1);

namespace Gwo\AppsRecruitmentTask\Domain\Document\LectureEnrollment;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Gwo\AppsRecruitmentTask\Domain\Enum\CollectionNameEnum;
use Gwo\AppsRecruitmentTask\Util\StringId;

#[ODM\Document(collection: CollectionNameEnum::LECTURE_ENROLLMENT->value)]
final readonly class LectureEnrollment
{
    #[ODM\Id(type: 'string', strategy: 'auto')]
    private StringId $id;

    #[ODM\Field(type: "string", nullable: false)]
    private StringId $lectureId;

    #[ODM\Field(type: "string", nullable: false)]
    private StringId $studentId;

    public function __construct(
        StringId $id,
        StringId $lectureId,
        StringId $studentId
    ) {
        $this->id = $id;
        $this->lectureId = $lectureId;
        $this->studentId = $studentId;
    }

    public function getId(): StringId
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
