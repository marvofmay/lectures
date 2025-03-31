<?php

declare(strict_types=1);

namespace Gwo\AppsRecruitmentTask\Domain\Service;

use Gwo\AppsRecruitmentTask\Application\Command\DeleteStudentFromLectureCommand;
use Gwo\AppsRecruitmentTask\Domain\Document\LectureEnrollment\LectureEnrollment;
use Gwo\AppsRecruitmentTask\Domain\Exception\NotFoundLectureEnrollmentException;
use Gwo\AppsRecruitmentTask\Domain\Interface\LectureEnrollment\LectureEnrollmentReaderInterface;
use Gwo\AppsRecruitmentTask\Domain\Interface\LectureEnrollment\LectureEnrollmentWriterInterface;
use Gwo\AppsRecruitmentTask\Util\StringId;
use Symfony\Contracts\Translation\TranslatorInterface;

readonly class StudentDeleter
{
    public function __construct(
        private LectureEnrollmentReaderInterface $lectureEnrollmentReaderRepository,
        private LectureEnrollmentWriterInterface $lectureEnrollmentWriterRepository,
        private TranslatorInterface $translator,
    ) {
    }

    public function delete(DeleteStudentFromLectureCommand $command): void
    {
        $lectureEnrollment = $this->lectureEnrollmentReaderRepository->getEnrolledStudentByLectureId($command->getLectureUUID(), $command->getStudentUUID());
        if (!$lectureEnrollment) {
            throw new NotFoundLectureEnrollmentException($this->translator->trans('lectureEnrollment.notFound', [':lectureUUID' => $command->getLectureUUID(), ':studentUUID' => $command->getStudentUUID()], 'lectureEnrollments'));
        }

        $this->lectureEnrollmentWriterRepository->deleteInDB(
            new LectureEnrollment(
                new StringId($lectureEnrollment['_id']),
                new StringId($lectureEnrollment['lectureId']),
                new StringId($lectureEnrollment['studentId'])),
        );
    }
}
