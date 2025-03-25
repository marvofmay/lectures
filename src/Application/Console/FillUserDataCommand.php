<?php

declare(strict_types=1);

namespace Gwo\AppsRecruitmentTask\Application\Console;

use Gwo\AppsRecruitmentTask\Domain\Document\User\User;
use Gwo\AppsRecruitmentTask\Domain\Enum\CollectionNameEnum;
use Gwo\AppsRecruitmentTask\Domain\Enum\UserRole;
use Gwo\AppsRecruitmentTask\Infrastructure\Persistence\MongoDB\DatabaseClient;
use Gwo\AppsRecruitmentTask\Util\StringId;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(name: 'app:fill-user-data')]
class FillUserDataCommand extends Command
{
    private const DESCRIPTION = 'Fills the User collection';
    private const HELP = 'This command allows you to populate the User collection.';
    private const SUCCESS_MESSAGE = 'User collection has been filled with data successfully!';

    public function __construct(
        private DatabaseClient $databaseClient,
        private UserPasswordHasherInterface $passwordHasher
    ) {
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
        $output->writeln('Filling User collection with data...');

        $users = [
            ['name' => 'Emma', 'role' => UserRole::LECTURER, 'password' => 'emma'],
            ['name' => 'Daniel', 'role' => UserRole::LECTURER, 'password' => 'daniel'],
            ['name' => 'Sophia', 'role' => UserRole::LECTURER, 'password' => 'sophia'],
            ['name' => 'Michael', 'role' => UserRole::STUDENT, 'password' => 'michael'],
            ['name' => 'Olivia', 'role' => UserRole::STUDENT, 'password' => 'olivia'],
            ['name' => 'Lucas', 'role' => UserRole::STUDENT, 'password' => 'lucas'],
            ['name' => 'Hannah', 'role' => UserRole::LECTURER, 'password' => 'hannah'],
            ['name' => 'William', 'role' => UserRole::STUDENT, 'password' => 'william'],
            ['name' => 'Natalie', 'role' => UserRole::LECTURER, 'password' => 'natalie'],
            ['name' => 'Ethan', 'role' => UserRole::STUDENT, 'password' => 'ethan'],
        ];

        foreach ($users as $userData) {
            $hashedPassword = $this->passwordHasher->hashPassword(
                new User(StringId::new(), $userData['name'], $userData['role']),
                $userData['password']
            );

            $user = new User(
                StringId::new(),
                $userData['name'],
                $userData['role'],
                $hashedPassword
            );

            $this->databaseClient->upsert(
                CollectionNameEnum::USER->value,
                ['_id' => (string) $user->getId()],
                [
                    '$set' => [
                        '_id' => (string) $user->getId(),
                        'name' => $user->getName(),
                        'role' => $user->getRole()->value,
                        'password' => $user->getPassword(),
                    ],
                ]
            );
        }

        $output->writeln(sprintf('<info>%s</info>', self::SUCCESS_MESSAGE));

        return Command::SUCCESS;
    }
}