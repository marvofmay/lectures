<?php

declare(strict_types=1);

namespace Gwo\AppsRecruitmentTask\Tests\Lecture\unit;

use Gwo\AppsRecruitmentTask\Domain\Document\Lecture\Lecture;
use Gwo\AppsRecruitmentTask\Domain\Document\User\User;
use Gwo\AppsRecruitmentTask\Domain\Enum\PermissionLectureEnum;
use Gwo\AppsRecruitmentTask\Domain\Enum\UserRole;
use Gwo\AppsRecruitmentTask\Domain\Security\Voter\LectureCreateVoter;
use Gwo\AppsRecruitmentTask\Util\StringId;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class LectureCreateVoterTest extends TestCase
{
    private LectureCreateVoter $voter;

    protected function setUp(): void
    {
        $this->voter = new LectureCreateVoter();
    }

    public function testVoteOnAttributeGrantsAccessForLecturer(): void
    {
        $user = new User(new StringId('123'), 'Test User', UserRole::LECTURER);

        $token = $this->createMock(TokenInterface::class);
        $token->method('getUser')->willReturn($user);

        $voteMethod = new \ReflectionMethod(LectureCreateVoter::class, 'voteOnAttribute');

        $result = $voteMethod->invoke($this->voter, PermissionLectureEnum::CREATE->value, Lecture::class, $token);

        $this->assertTrue($result);
    }

    public function testVoteOnAttributeDeniesAccessForNonLecturer(): void
    {
        $user = new User(new StringId('456'), 'Student User', UserRole::STUDENT);

        $token = $this->createMock(TokenInterface::class);
        $token->method('getUser')->willReturn($user);

        $voteMethod = new \ReflectionMethod(LectureCreateVoter::class, 'voteOnAttribute');

        $result = $voteMethod->invoke($this->voter, PermissionLectureEnum::CREATE->value, Lecture::class, $token);

        $this->assertFalse($result);
    }

    public function testVoteOnAttributeDeniesAccessForAnonymousUser(): void
    {
        $token = $this->createMock(TokenInterface::class);
        $token->method('getUser')->willReturn(null);

        $voteMethod = new \ReflectionMethod(LectureCreateVoter::class, 'voteOnAttribute');

        $result = $voteMethod->invoke($this->voter, PermissionLectureEnum::CREATE->value, Lecture::class, $token);

        $this->assertFalse($result);
    }
}
