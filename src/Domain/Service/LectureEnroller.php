<?php

declare(strict_types=1);

namespace Gwo\AppsRecruitmentTask\Domain\Service;

use Gwo\AppsRecruitmentTask\Application\Command\EnrollLectureCommand;
use Gwo\AppsRecruitmentTask\Domain\Document\LectureEnrollment\LectureEnrollment;
use Gwo\AppsRecruitmentTask\Domain\Interface\Lecture\LectureReaderInterface;
use Gwo\AppsRecruitmentTask\Domain\Interface\LectureEnrollment\LectureEnrollmentReaderInterface;
use Gwo\AppsRecruitmentTask\Domain\Interface\LectureEnrollment\LectureEnrollmentWriterInterface;
use Gwo\AppsRecruitmentTask\Util\StringId;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\User\UserInterface;

readonly class LectureEnroller
{
    public function __construct(
        private LectureReaderInterface $lectureReaderRepository,
        private LectureEnrollmentReaderInterface $lectureEnrollmentReaderRepository,
        private LectureEnrollmentWriterInterface $lectureEnrollmentWriterRepository,
        private Security $security,
    ) {
    }

    public function enroll(EnrollLectureCommand $command): void
    {
        $user = $this->security->getUser();
        if (!$user instanceof UserInterface) {
            throw new \LogicException('Musisz być zalogowany.');
        }

        $lecture = $this->lectureReaderRepository->findByUUID($command->getLectureUUID());
        if (!$lecture) {
            throw new \RuntimeException('Nie znaleziono wykładu.');
        }

        if ($this->lectureEnrollmentReaderRepository->countEnrolledStudentsByLectureId((string)$lecture->getId()) >= $lecture->getStudentLimit()) {
            throw new \RuntimeException('Brak miejsc na ten wykład.');
        }

        $now = new \DateTimeImmutable();
        if ($lecture->getEndDate() <= $now) {
            throw new \RuntimeException('Nie można zapisać - wykład już się zakończył.');
        }

        if ($lecture->getStartDate() <= $now && $lecture->getEndDate() > $now) {
            throw new \RuntimeException('Nie można zapisać - wykład już trwa.');
        }

        if ($this->lectureEnrollmentReaderRepository->isStudentAlreadyEnrolled((string) $lecture->getId(), (string) $user->getId())) {
            throw new \RuntimeException('Jesteś już zapisana/y na ten wykład.');
        }

        $lectureEnrollment = new LectureEnrollment(
            StringId::new(),
            new StringId($command->getLectureUUID()),
            $user->getId(),
        );

        $this->lectureEnrollmentWriterRepository->saveInDB($lectureEnrollment);
    }
}