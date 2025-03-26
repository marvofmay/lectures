<?php

declare(strict_types=1);

namespace Gwo\AppsRecruitmentTask\Application\Transformer;

use Doctrine\Common\Collections\Collection;

final class LectureTransformer
{
    public static function transform(array $lecture): array
    {
        $c = $lecture;

        return [
            'name' => $lecture['name'],
            'lecturerName' => $lecture['lecturerName'],
            'startDate' => $lecture['startDate']->format('Y-m-d H:i'),
            'endDate' => $lecture['endDate']->format('Y-m-d H:i'),
            'studentLimit' => $lecture['studentLimit'],
        ];
    }

    public static function transformCollection(Collection $lectures): array
    {
        return $lectures->map(fn (array $lecture) => self::transform($lecture))->toArray();
    }
}