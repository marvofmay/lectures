<?php

declare(strict_types=1);

namespace Gwo\AppsRecruitmentTask\Domain\Service;

use Gwo\AppsRecruitmentTask\Application\Command\DeleteStudentFromLectureCommand;
use Gwo\AppsRecruitmentTask\Domain\Document\LectureEnrollment\LectureEnrollment;
use Gwo\AppsRecruitmentTask\Domain\Interface\LectureEnrollment\LectureEnrollmentReaderInterface;
use Gwo\AppsRecruitmentTask\Domain\Interface\LectureEnrollment\LectureEnrollmentWriterInterface;
use Gwo\AppsRecruitmentTask\Util\StringId;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\User\UserInterface;

readonly class StudentDeleter
{
    public function __construct(
        private LectureEnrollmentReaderInterface $lectureEnrollmentReaderRepository,
        private LectureEnrollmentWriterInterface $lectureEnrollmentWriterRepository,
        private Security $security,
    ) {
    }

    public function delete(DeleteStudentFromLectureCommand $command): void
    {
        $user = $this->security->getUser();
        if (!$user instanceof UserInterface) {
            throw new \LogicException('Musisz być zalogowany.');
        }

        $lectureEnrollment = $this->lectureEnrollmentReaderRepository->getEnrolledStudentByLectureId($command->getLectureUUID(), $command->getStudentUUID());
        if (!$lectureEnrollment) {
            throw new \RuntimeException('Nie znaleziono studenta zapisanego na ten wykład.');
        }

        $this->lectureEnrollmentWriterRepository->deleteInDB(
            new LectureEnrollment(
                new StringId($lectureEnrollment['_id']),
                new StringId($lectureEnrollment['lectureId']),
                new StringId($lectureEnrollment['studentId'])),
        );
    }
}