<?php

declare(strict_types=1);

namespace Gwo\AppsRecruitmentTask\Tests\Lecture\unit;

use DateTimeImmutable;
use Gwo\AppsRecruitmentTask\Domain\Document\Lecture\Lecture;
use Gwo\AppsRecruitmentTask\Util\StringId;
use PHPUnit\Framework\TestCase;

class LectureTest extends TestCase
{
    public function testLectureConstructorAndGetters(): void
    {
        $id = new StringId('lecture-id');
        $lecturerId = new StringId('lecturer-id');
        $name = 'Lecture about PHP';
        $studentLimit = 30;
        $startDate = new DateTimeImmutable('2025-03-29 08:00');
        $endDate = new DateTimeImmutable('2025-03-29 15:00');

        $lecture = new Lecture(
            $id,
            $lecturerId,
            $name,
            $studentLimit,
            $startDate,
            $endDate
        );

        $this->assertEquals($id, $lecture->getId());
        $this->assertEquals($lecturerId, $lecture->getLecturerId());
        $this->assertEquals($name, $lecture->getName());
        $this->assertEquals($studentLimit, $lecture->getStudentLimit());
        $this->assertEquals($startDate, $lecture->getStartDate());
        $this->assertEquals($endDate, $lecture->getEndDate());
    }
}