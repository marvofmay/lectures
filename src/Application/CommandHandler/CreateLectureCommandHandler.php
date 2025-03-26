<?php

declare(strict_types=1);

namespace Gwo\AppsRecruitmentTask\Application\CommandHandler;

use Gwo\AppsRecruitmentTask\Application\Command\CreateLectureCommand;
use Gwo\AppsRecruitmentTask\Domain\Service\LectureCreator;

readonly class CreateLectureCommandHandler
{
    public function __construct(private LectureCreator $lectureCreator)
    {
    }

    public function __invoke(CreateLectureCommand $command): void
    {
        $this->lectureCreator->create($command);
    }
}
