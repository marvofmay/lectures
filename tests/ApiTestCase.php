<?php

declare(strict_types=1);

namespace Gwo\AppsRecruitmentTask\Tests;

use Gwo\AppsRecruitmentTask\Domain\Document\User\User;
use Gwo\AppsRecruitmentTask\Domain\Enum\DocumentNameEnum;
use Gwo\AppsRecruitmentTask\Domain\Enum\UserDocumentFieldEnum;
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
    protected const LECTURER_EMMA = 'Emma';
    protected const LECTURER_DANIEL = 'Daniel';
    protected const LECTURER_NATALIE = 'Natalie';
    protected const STUDENT_ETHAN = 'Ethan';
    protected const STUDENT_OLIVIA = 'Olivia';
    protected const STUDENT_MICHAEL = 'Michael';
    protected const ROLE_STUDENT = 'student';

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
            [UserDocumentFieldEnum::NAME->value => 'Emma', UserDocumentFieldEnum::ROLE->value => UserRole::LECTURER, UserDocumentFieldEnum::PASSWORD->value => 'emma'],
            [UserDocumentFieldEnum::NAME->value => 'Daniel', UserDocumentFieldEnum::ROLE->value => UserRole::LECTURER, UserDocumentFieldEnum::PASSWORD->value => 'daniel'],
            [UserDocumentFieldEnum::NAME->value => 'Sophia', UserDocumentFieldEnum::ROLE->value => UserRole::LECTURER, UserDocumentFieldEnum::PASSWORD->value => 'sophia'],
            [UserDocumentFieldEnum::NAME->value => 'Michael', UserDocumentFieldEnum::ROLE->value => UserRole::STUDENT, UserDocumentFieldEnum::PASSWORD->value => 'michael'],
            [UserDocumentFieldEnum::NAME->value => 'Olivia', UserDocumentFieldEnum::ROLE->value => UserRole::STUDENT, UserDocumentFieldEnum::PASSWORD->value => 'olivia'],
            [UserDocumentFieldEnum::NAME->value => 'Lucas', UserDocumentFieldEnum::ROLE->value => UserRole::STUDENT, UserDocumentFieldEnum::PASSWORD->value => 'lucas'],
            [UserDocumentFieldEnum::NAME->value => 'Hannah', UserDocumentFieldEnum::ROLE->value => UserRole::LECTURER, UserDocumentFieldEnum::PASSWORD->value => 'hannah'],
            [UserDocumentFieldEnum::NAME->value => 'William', UserDocumentFieldEnum::ROLE->value => UserRole::STUDENT, UserDocumentFieldEnum::PASSWORD->value => 'william'],
            [UserDocumentFieldEnum::NAME->value => 'Natalie', UserDocumentFieldEnum::ROLE->value => UserRole::LECTURER, UserDocumentFieldEnum::PASSWORD->value => 'natalie'],
            [UserDocumentFieldEnum::NAME->value => 'Ethan', UserDocumentFieldEnum::ROLE->value => UserRole::STUDENT, UserDocumentFieldEnum::PASSWORD->value => 'ethan'],
        ];

        foreach ($users as $userData) {
            $user = new User(
                StringId::new(),
                $userData[UserDocumentFieldEnum::NAME->value],
                $userData[UserDocumentFieldEnum::ROLE->value]
            );

            $hashedPassword = $passwordHasher->hashPassword(
                $user,
                $userData[UserDocumentFieldEnum::PASSWORD->value]
            );

            $user = new User(
                StringId::new(),
                $userData[UserDocumentFieldEnum::NAME->value],
                $userData[UserDocumentFieldEnum::ROLE->value],
                $userData[UserDocumentFieldEnum::PASSWORD->value],
            );

            $databaseClient->upsert(
                DocumentNameEnum::USER->value,
                [UserDocumentFieldEnum::ID->value => (string) $user->getId()],
                [
                    '$set' => [
                        UserDocumentFieldEnum::ID->value => (string) $user->getId(),
                        UserDocumentFieldEnum::NAME->value => $user->getName(),
                        UserDocumentFieldEnum::ROLE->value => $user->getRole()->value,
                        UserDocumentFieldEnum::PASSWORD->value => $hashedPassword,
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
            'startDate' => (new \DateTime('+1 day'))->format('Y-m-d H:i'),
            'endDate' => (new \DateTime('+1 day +4 hours'))->format('Y-m-d H:i'),
        ];

        $this->makeRequest('POST', '/api/lectures', json_encode($lectureData), [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
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
            'startDate' => (new \DateTime('-3 day'))->format('Y-m-d H:i'),
            'endDate' => (new \DateTime('-3 day +4 hours'))->format('Y-m-d H:i'),
        ];

        $this->makeRequest('POST', '/api/lectures', json_encode($lectureData), [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
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
            'startDate' => (new \DateTime('+1 day'))->format('Y-m-d H:i'),
            'endDate' => (new \DateTime('+1 day +4 hours'))->format('Y-m-d H:i'),
        ];

        $this->makeRequest('POST', '/api/lectures', json_encode($lectureData), [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
        ]);
    }

    protected function loginUser(string $name = self::LECTURER_EMMA): string
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
