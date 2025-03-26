<?php

declare(strict_types=1);

namespace Gwo\AppsRecruitmentTask\Application\Console;

use Gwo\AppsRecruitmentTask\Domain\Document\User\User;
use Gwo\AppsRecruitmentTask\Domain\Enum\CollectionNameEnum;
use Gwo\AppsRecruitmentTask\Domain\Enum\UserCollectionColumnEnum;
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
            [UserCollectionColumnEnum::NAME->value => 'Emma', UserCollectionColumnEnum::ROLE->value => UserRole::LECTURER, UserCollectionColumnEnum::PASSWORD->value => 'emma'],
            [UserCollectionColumnEnum::NAME->value => 'Daniel', UserCollectionColumnEnum::ROLE->value => UserRole::LECTURER, UserCollectionColumnEnum::PASSWORD->value => 'daniel'],
            [UserCollectionColumnEnum::NAME->value => 'Sophia', UserCollectionColumnEnum::ROLE->value => UserRole::LECTURER, UserCollectionColumnEnum::PASSWORD->value => 'sophia'],
            [UserCollectionColumnEnum::NAME->value => 'Michael', UserCollectionColumnEnum::ROLE->value => UserRole::STUDENT, UserCollectionColumnEnum::PASSWORD->value => 'michael'],
            [UserCollectionColumnEnum::NAME->value => 'Olivia', UserCollectionColumnEnum::ROLE->value => UserRole::STUDENT, UserCollectionColumnEnum::PASSWORD->value => 'olivia'],
            [UserCollectionColumnEnum::NAME->value => 'Lucas', UserCollectionColumnEnum::ROLE->value => UserRole::STUDENT, UserCollectionColumnEnum::PASSWORD->value => 'lucas'],
            [UserCollectionColumnEnum::NAME->value => 'Hannah', UserCollectionColumnEnum::ROLE->value => UserRole::LECTURER, UserCollectionColumnEnum::PASSWORD->value => 'hannah'],
            [UserCollectionColumnEnum::NAME->value => 'William', UserCollectionColumnEnum::ROLE->value => UserRole::STUDENT, UserCollectionColumnEnum::PASSWORD->value => 'william'],
            [UserCollectionColumnEnum::NAME->value => 'Natalie', UserCollectionColumnEnum::ROLE->value => UserRole::LECTURER, UserCollectionColumnEnum::PASSWORD->value => 'natalie'],
            [UserCollectionColumnEnum::NAME->value => 'Ethan', UserCollectionColumnEnum::ROLE->value => UserRole::STUDENT, UserCollectionColumnEnum::PASSWORD->value => 'ethan'],
        ];

        foreach ($users as $userData) {
            $hashedPassword = $this->passwordHasher->hashPassword(
                new User(StringId::new(), $userData[UserCollectionColumnEnum::NAME->value], $userData[UserCollectionColumnEnum::ROLE->value]),
                $userData[UserCollectionColumnEnum::PASSWORD->value]
            );

            $user = new User(
                StringId::new(),
                $userData[UserCollectionColumnEnum::NAME->value],
                $userData[UserCollectionColumnEnum::ROLE->value],
                $hashedPassword
            );

            $this->databaseClient->upsert(
                CollectionNameEnum::USER->value,
                [UserCollectionColumnEnum::ID->value => (string) $user->getId()],
                [
                    '$set' => [
                        UserCollectionColumnEnum::ID->value => (string) $user->getId(),
                        UserCollectionColumnEnum::NAME->value => $user->getName(),
                        UserCollectionColumnEnum::ROLE->value => $user->getRole()->value,
                        UserCollectionColumnEnum::PASSWORD->value => $user->getPassword(),
                    ],
                ]
            );
        }

        $output->writeln(sprintf('<info>%s</info>', self::SUCCESS_MESSAGE));

        return Command::SUCCESS;
    }
}
