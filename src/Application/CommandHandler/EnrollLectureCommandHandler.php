<?php

declare(strict_types=1);

namespace Gwo\AppsRecruitmentTask\Application\CommandHandler;

use Gwo\AppsRecruitmentTask\Application\Command\EnrollLectureCommand;
use Gwo\AppsRecruitmentTask\Domain\Service\LectureEnroller;

readonly class EnrollLectureCommandHandler
{
    public function __construct(private LectureEnroller $lectureEnroller)
    {
    }

    public function __invoke(EnrollLectureCommand $command): void
    {
        $this->lectureEnroller->enroll($command);
    }
}
