<?php

declare(strict_types=1);

namespace Gwo\AppsRecruitmentTask\Tests\Lecture\unit;

use Gwo\AppsRecruitmentTask\Domain\Document\LectureEnrollment\LectureEnrollment;
use Gwo\AppsRecruitmentTask\Util\StringId;
use PHPUnit\Framework\TestCase;

class LectureEnrollmentTest extends TestCase
{
    public function testLectureEnrollmentCreation(): void
    {
        $id = new StringId('123');
        $lectureId = new StringId('456');
        $studentId = new StringId('789');

        $enrollment = new LectureEnrollment($id, $lectureId, $studentId);

        $this->assertSame($id, $enrollment->getId());
        $this->assertSame($lectureId, $enrollment->getLectureId());
        $this->assertSame($studentId, $enrollment->getStudentId());
    }
}