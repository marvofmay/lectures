<?php

declare(strict_types=1);

namespace Gwo\AppsRecruitmentTask\Domain\Service;

use Gwo\AppsRecruitmentTask\Application\Command\CreateLectureCommand;
use Gwo\AppsRecruitmentTask\Domain\Document\Lecture\Lecture;
use Gwo\AppsRecruitmentTask\Domain\Interface\Lecture\LectureWriterInterface;
use Gwo\AppsRecruitmentTask\Util\StringId;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\User\UserInterface;

readonly class LectureCreator
{
    public function __construct(private LectureWriterInterface $lectureWriterRepository, private Security $security,)
    {
    }

    public function create(CreateLectureCommand $command): void
    {
        $user = $this->security->getUser();

        if (!$user instanceof UserInterface) {
            throw new \LogicException('User must be logged in to create a lecture.');
        }

        $lecture = new Lecture(
            StringId::new(),
            $user->getId(),
            $command->getName(),
            $command->getStudentLimit(),
            new \DateTimeImmutable($command->getStartDate()),
            new \DateTimeImmutable($command->getEndDate()),
        );

        $this->lectureWriterRepository->saveInDB($lecture);
    }
}