<?php

declare(strict_types=1);

namespace Gwo\AppsRecruitmentTask\Application\Console;

use Gwo\AppsRecruitmentTask\Domain\Document\User\User;
use Gwo\AppsRecruitmentTask\Domain\Enum\CollectionNameEnum;
use Gwo\AppsRecruitmentTask\Domain\Enum\UserDocumentFieldEnum;
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
        private UserPasswordHasherInterface $passwordHasher,
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
            [UserDocumentFieldEnum::NAME->value => 'Emma', UserDocumentFieldEnum::ROLE->value => UserRole::LECTURER],
            [UserDocumentFieldEnum::NAME->value => 'Daniel', UserDocumentFieldEnum::ROLE->value => UserRole::LECTURER],
            [UserDocumentFieldEnum::NAME->value => 'Sophia', UserDocumentFieldEnum::ROLE->value => UserRole::LECTURER],
            [UserDocumentFieldEnum::NAME->value => 'Michael', UserDocumentFieldEnum::ROLE->value => UserRole::STUDENT],
            [UserDocumentFieldEnum::NAME->value => 'Olivia', UserDocumentFieldEnum::ROLE->value => UserRole::STUDENT],
            [UserDocumentFieldEnum::NAME->value => 'Lucas', UserDocumentFieldEnum::ROLE->value => UserRole::STUDENT],
            [UserDocumentFieldEnum::NAME->value => 'Hannah', UserDocumentFieldEnum::ROLE->value => UserRole::LECTURER],
            [UserDocumentFieldEnum::NAME->value => 'William', UserDocumentFieldEnum::ROLE->value => UserRole::STUDENT],
            [UserDocumentFieldEnum::NAME->value => 'Natalie', UserDocumentFieldEnum::ROLE->value => UserRole::LECTURER],
            [UserDocumentFieldEnum::NAME->value => 'Ethan', UserDocumentFieldEnum::ROLE->value => UserRole::STUDENT],
        ];

        foreach ($users as $userData) {
            $hashedPassword = $this->passwordHasher->hashPassword(
                new User(StringId::new(), $userData[UserDocumentFieldEnum::NAME->value], $userData[UserDocumentFieldEnum::ROLE->value]),
                lcfirst($userData[UserDocumentFieldEnum::NAME->value])
            );

            $user = new User(
                StringId::new(),
                $userData[UserDocumentFieldEnum::NAME->value],
                $userData[UserDocumentFieldEnum::ROLE->value],
                $hashedPassword
            );

            $this->databaseClient->upsert(
                CollectionNameEnum::USER->value,
                [UserDocumentFieldEnum::ID->value => (string) $user->getId()],
                [
                    '$set' => [
                        UserDocumentFieldEnum::ID->value => (string) $user->getId(),
                        UserDocumentFieldEnum::NAME->value => $user->getName(),
                        UserDocumentFieldEnum::ROLE->value => $user->getRole()->value,
                        UserDocumentFieldEnum::PASSWORD->value => $user->getPassword(),
                    ],
                ]
            );
        }

        $output->writeln(sprintf('<info>%s</info>', self::SUCCESS_MESSAGE));

        return Command::SUCCESS;
    }
}
