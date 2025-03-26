<?php

declare(strict_types=1);

namespace Gwo\AppsRecruitmentTask\Application\CommandHandler;

use Gwo\AppsRecruitmentTask\Application\Command\DeleteStudentFromLectureCommand;
use Gwo\AppsRecruitmentTask\Domain\Service\StudentDeleter;


readonly class DeleteStudentFromLectureCommandHandler
{
    public function __construct(private StudentDeleter $studentDeleter)
    {
    }

    public function __invoke(DeleteStudentFromLectureCommand $command): void
    {
        $this->studentDeleter->delete($command);
    }
}
