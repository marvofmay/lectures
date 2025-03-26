<?php

declare(strict_types=1);

namespace Gwo\AppsRecruitmentTask\Domain\Security\Voter;

use Gwo\AppsRecruitmentTask\Domain\Document\Lecture\Lecture;
use Gwo\AppsRecruitmentTask\Domain\Enum\PermissionLectureEnum;
use Gwo\AppsRecruitmentTask\Domain\Enum\UserRole;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

final class LectureListVoter extends Voter
{
    protected function supports(string $attribute, mixed $subject): bool
    {
        return $attribute === PermissionLectureEnum::LIST_LECTURES->value && Lecture::class === $subject;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        if (!$token->getUser() instanceof UserInterface) {
            return false;
        }

        return UserRole::STUDENT === $token->getUser()->getRole();
    }
}
