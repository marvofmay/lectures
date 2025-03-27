<?php

declare(strict_types=1);

namespace Gwo\AppsRecruitmentTask\Tests\Lecture\unit;

use Gwo\AppsRecruitmentTask\Domain\Document\User\User;
use Gwo\AppsRecruitmentTask\Domain\Enum\UserRole;
use Gwo\AppsRecruitmentTask\Util\StringId;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    public function testUserCreation(): void
    {
        $id = new StringId('123');
        $name = 'John Doe';
        $role = UserRole::LECTURER;
        $password = 'securePassword123';

        $user = new User($id, $name, $role, $password);

        $this->assertSame($id, $user->getId());
        $this->assertSame($name, $user->getName());
        $this->assertSame($role, $user->getRole());
        $this->assertSame([$role->value], $user->getRoles());
        $this->assertSame($name, $user->getUserIdentifier());
        $this->assertSame($password, $user->getPassword());
    }

    public function testEraseCredentials(): void
    {
        $id = new StringId('123');
        $user = new User($id, 'Jane Doe', UserRole::STUDENT, 'tempPassword');

        $this->assertNotNull($user->getPassword());

        $user->eraseCredentials();

        $this->assertNull($user->getPassword());
    }
}