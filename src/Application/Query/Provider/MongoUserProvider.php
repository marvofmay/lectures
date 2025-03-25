<?php

declare(strict_types=1);

namespace Gwo\AppsRecruitmentTask\Application\Query\Provider;

namespace Gwo\AppsRecruitmentTask\Application\Query\Provider;

use Gwo\AppsRecruitmentTask\Domain\Document\User\User;
use Gwo\AppsRecruitmentTask\Domain\Enum\UserRole;
use Gwo\AppsRecruitmentTask\Infrastructure\Persistence\MongoDB\DatabaseClient;
use Gwo\AppsRecruitmentTask\Util\StringId;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class MongoUserProvider implements UserProviderInterface
{

    public function __construct(
        private DatabaseClient $databaseClient,
        private UserPasswordHasherInterface $passwordHasher,
    ) {

    }

    public function loadUserByIdentifier(string $identifier): UserInterface
    {
        $userData = $this->databaseClient->getByQuery('User', ['name' => $identifier]);

        if (!$userData) {
            throw new UserNotFoundException("User '$identifier' not found.");
        }

        return new User(
            new StringId($userData[0]['_id']),
            $userData[0]['name'],
            UserRole::from($userData[0]['role']),
            $userData[0]['password']
        );
    }


    public function verifyPassword(UserInterface $user, string $rawPassword): bool
    {

        return $this->passwordHasher->isPasswordValid($user, $rawPassword);
    }

    public function refreshUser(UserInterface $user): UserInterface
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException('Invalid user class');
        }

        return $this->loadUserByIdentifier($user->getUserIdentifier());
    }

    public function supportsClass(string $class): bool
    {
        return $class === User::class;
    }
}