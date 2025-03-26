<?php

declare(strict_types=1);

namespace Gwo\AppsRecruitmentTask\Tests;

use Gwo\AppsRecruitmentTask\Domain\Document\User\User;
use Gwo\AppsRecruitmentTask\Domain\Enum\CollectionNameEnum;
use Gwo\AppsRecruitmentTask\Domain\Enum\UserCollectionColumnEnum;
use Gwo\AppsRecruitmentTask\Domain\Enum\UserRole;
use Gwo\AppsRecruitmentTask\Infrastructure\Persistence\MongoDB\DatabaseClient;
use Gwo\AppsRecruitmentTask\Util\StringId;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

abstract class ApiTestCase extends WebTestCase
{
    protected const LECTURE_NAME = 'Lecture about education';
    protected const LECTURE_EXPIRED_NAME = 'Lecture about IT';
    protected const LECTURE_LIMITED_NAME = 'Lecture about OZE';

    protected readonly KernelBrowser $httpClient;

    protected function setUp(): void
    {
        $this->httpClient = static::createClient();

        /** @var DatabaseClient $databaseClient */
        $databaseClient = $this->httpClient->getContainer()->get(DatabaseClient::class);
        $databaseClient->dropDatabase();
        $passwordHasher = $this->httpClient->getContainer()->get(UserPasswordHasherInterface::class);
        $this->fillUserDatabase($databaseClient, $passwordHasher);
        $this->addLecture();
    }

    protected function fillUserDatabase($databaseClient, $passwordHasher): void
    {
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
            $user = new User(
                StringId::new(),
                $userData[UserCollectionColumnEnum::NAME->value],
                $userData[UserCollectionColumnEnum::ROLE->value]
            );

            $hashedPassword = $passwordHasher->hashPassword(
                $user,
                $userData[UserCollectionColumnEnum::PASSWORD->value]
            );

            $user = new User(
                StringId::new(),
                $userData[UserCollectionColumnEnum::NAME->value],
                $userData[UserCollectionColumnEnum::ROLE->value],
                $userData[UserCollectionColumnEnum::PASSWORD->value],
            );

            $databaseClient->upsert(
                CollectionNameEnum::USER->value,
                [UserCollectionColumnEnum::ID->value => (string) $user->getId()],
                [
                    '$set' => [
                        UserCollectionColumnEnum::ID->value => (string) $user->getId(),
                        UserCollectionColumnEnum::NAME->value => $user->getName(),
                        UserCollectionColumnEnum::ROLE->value => $user->getRole()->value,
                        UserCollectionColumnEnum::PASSWORD->value => $hashedPassword,
                    ],
                ]
            );
        }
    }

    protected function makeRequest(string $method, string $uri, string $content = '', array $headers = []): Response
    {
        $this->httpClient->request(
            $method,
            $uri,
            [],
            [],
            $headers,
            $content,
        );

        return $this->httpClient->getResponse();
    }

    protected function getLectureIdByName($name): string
    {
        /** @var DatabaseClient $databaseClient */
        $databaseClient = $this->httpClient->getContainer()->get(DatabaseClient::class);
        $lecture = $databaseClient->getByQuery('Lecture', ['name' => $name]);

        return $lecture[0]['_id'];
    }

    protected function getUserIdByName($name, $role): string
    {
        /** @var DatabaseClient $databaseClient */
        $databaseClient = $this->httpClient->getContainer()->get(DatabaseClient::class);
        $lecturer = $databaseClient->getByQuery('User', ['name' => $name, 'role' => $role]);

        return $lecturer[0]['_id'];
    }

    protected function addLecture(): void
    {
        $loginData = [
            'name' => 'Emma',
            'password' => 'emma',
        ];
        $loginResponse = $this->makeRequest('POST', '/api/login', json_encode($loginData), [
            'CONTENT_TYPE' => 'application/json',
        ]);
        $data = json_decode($loginResponse->getContent(), true);
        $token = $data['token'];

        $lectureData = [
            'name' => self::LECTURE_NAME,
            'studentLimit' => 10,
            'startDate' => '2025-03-29 08:00',
            'endDate' => '2025-03-29 15:00',
        ];

        $this->makeRequest('POST', '/api/lectures', json_encode($lectureData), [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_AUTHORIZATION' => 'Bearer ' . $token,
        ]);
    }

    protected function addExpiredLecture(): void
    {
        $loginData = [
            'name' => 'Emma',
            'password' => 'emma',
        ];
        $loginResponse = $this->makeRequest('POST', '/api/login', json_encode($loginData), [
            'CONTENT_TYPE' => 'application/json',
        ]);
        $data = json_decode($loginResponse->getContent(), true);
        $token = $data['token'];

        $lectureData = [
            'name' => self::LECTURE_EXPIRED_NAME,
            'studentLimit' => 10,
            'startDate' => '2025-03-12 08:00',
            'endDate' => '2025-03-12 15:00',
        ];

        $this->makeRequest('POST', '/api/lectures', json_encode($lectureData), [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_AUTHORIZATION' => 'Bearer ' . $token,
        ]);
    }

    protected function addLimitedLecture(): void
    {
        $loginData = [
            'name' => 'Emma',
            'password' => 'emma',
        ];
        $loginResponse = $this->makeRequest('POST', '/api/login', json_encode($loginData), [
            'CONTENT_TYPE' => 'application/json',
        ]);
        $data = json_decode($loginResponse->getContent(), true);
        $token = $data['token'];

        $lectureData = [
            'name' => self::LECTURE_LIMITED_NAME,
            'studentLimit' => 1,
            'startDate' => '2025-04-15 08:00',
            'endDate' => '2025-04-15 15:00',
        ];

        $this->makeRequest('POST', '/api/lectures', json_encode($lectureData), [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_AUTHORIZATION' => 'Bearer ' . $token,
        ]);
    }

    protected function loginAsStudent(string $name = 'Ethan'): string
    {
        $loginData = [
            'name' => $name,
            'password' => lcfirst($name),
        ];
        $loginResponse = $this->makeRequest('POST', '/api/login', json_encode($loginData), [
            'CONTENT_TYPE' => 'application/json',
        ]);
        $data = json_decode($loginResponse->getContent(), true);

        return $data['token'];
    }

    public function loginAsLecturer(string $name = 'Sophia'): string
    {
        $loginData = [
            'name' => $name,
            'password' => lcfirst($name),
        ];
        $loginResponse = $this->makeRequest('POST', '/api/login', json_encode($loginData), [
            'CONTENT_TYPE' => 'application/json',
        ]);
        $data = json_decode($loginResponse->getContent(), true);

        return $data['token'];
    }
}
