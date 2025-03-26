<?php

declare(strict_types=1);

namespace Gwo\AppsRecruitmentTask\Application\Transformer;

use Doctrine\Common\Collections\ArrayCollection;
use Gwo\AppsRecruitmentTask\Domain\Document\Lecture\Lecture;

final class LectureTransformer
{
    public static function transform(Lecture $lecture): array
    {
        return [
            'name' => $lecture->getName(),
            'startDate' => $lecture->getStartDate()->format('Y-m-d H:i'),
            'endDate' => $lecture->getEndDate()->format('Y-m-d H:i'),
            'studentLimit' => $lecture->getStudentLimit(),
        ];
    }

    public static function transformCollection(ArrayCollection $lectures): array
    {
        return $lectures->map(fn (Lecture $lecture) => self::transform($lecture))->toArray();
    }
}