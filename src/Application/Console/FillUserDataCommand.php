<?php

declare(strict_types=1);

namespace Gwo\AppsRecruitmentTask\Application\Console;

use Gwo\AppsRecruitmentTask\Domain\Document\User\User;
use Gwo\AppsRecruitmentTask\Domain\Document\User\UserRole;
use Gwo\AppsRecruitmentTask\Persistence\DatabaseClient;
use Gwo\AppsRecruitmentTask\Util\StringId;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:fill-user-data')]
class FillUserDataCommand extends Command
{
    private const DESCRIPTION = 'Fills the User collection';
    private const HELP = 'This command allows you to populate the User collection.';
    private const SUCCESS_MESSAGE = 'User collection has been filled with data successfully!';

    public function __construct(private DatabaseClient $databaseClient)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setDescription(self::DESCRIPTION)
            ->setHelp(self::HELP);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('Filling Employee table with data...');

        $users = [
            new User(StringId::new(), 'Emma', UserRole::LECTURER),
            new User(StringId::new(), 'Daniel', UserRole::LECTURER),
            new User(StringId::new(), 'Sophia', UserRole::LECTURER),
            new User(StringId::new(), 'Michael', UserRole::STUDENT),
            new User(StringId::new(), 'Olivia', UserRole::STUDENT),
            new User(StringId::new(), 'Lucas', UserRole::STUDENT),
            new User(StringId::new(), 'Hannah', UserRole::LECTURER),
            new User(StringId::new(), 'William', UserRole::STUDENT),
            new User(StringId::new(), 'Natalie', UserRole::LECTURER),
            new User(StringId::new(), 'Ethan', UserRole::STUDENT),
        ];

        foreach ($users as $user) {
            $this->databaseClient->upsert(
                'User',
                ['_id' => (string) $user->getId()],
                [
                    '$set' => [
                        '_id' => (string) $user->getId(),
                        'name' => $user->getName(),
                        'role' => $user->getRole()->value,
                    ],
                ]
            );
        }

        $output->writeln(sprintf('<info>%s</info>', self::SUCCESS_MESSAGE));

        return Command::SUCCESS;
    }
}
