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
use Symfony\Component\HttpFoundation\Response;
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
        $lectureEnrollment = new LectureEnrollment(
            StringId::new(),
            new StringId($command->getLectureUUID()),
            new StringId($command->getStudentUUID()),
        );

        $this->lectureEnrollmentWriterRepository->saveInDB($lectureEnrollment);
    }

    public function validateBeforeQueuedEnrollment(string $lectureUUID): array
    {
        $errors = [];
        $user = $this->security->getUser();
        if (!$user instanceof UserInterface) {
            $errors = ['message' => 'Musisz być zalogowany.', 'code' => Response::HTTP_UNAUTHORIZED];
        }

        $lecture = $this->lectureReaderRepository->findByUUID($lectureUUID);
        if (!$lecture) {
            $errors = ['message' => 'Nie znaleziono wykładu.', 'code' => Response::HTTP_NOT_FOUND];
        }

        if ($this->lectureEnrollmentReaderRepository->countEnrolledStudentsByLectureId((string) $lecture->getId()) >= $lecture->getStudentLimit()) {
            $errors = ['message' => 'Brak miejsc na ten wykład.', 'code' => Response::HTTP_CONFLICT];
        }

        $now = new \DateTimeImmutable();
        if ($lecture->getEndDate() <= $now) {
            $errors = ['message' => 'Nie można zapisać - wykład już się zakończył.', 'code' => Response::HTTP_BAD_REQUEST];
        }

        if ($lecture->getStartDate() <= $now && $lecture->getEndDate() > $now) {
            $errors = ['message' => 'Nie można zapisać - wykład już trwa.', 'code' => Response::HTTP_BAD_REQUEST];
        }

        if ($this->lectureEnrollmentReaderRepository->isStudentAlreadyEnrolled((string) $lecture->getId(), (string) $user->getId())) {
            $errors = ['message' => 'Jesteś już zapisana/y na ten wykład.', 'code' => Response::HTTP_CONFLICT];
        }

        return $errors;
    }
}
