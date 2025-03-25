<?php

namespace Gwo\AppsRecruitmentTask\Repository;

use Doctrine\ODM\MongoDB\Repository\DocumentRepository;
use Gwo\AppsRecruitmentTask\Domain\Document\User\User;

class UserRepository extends DocumentRepository
{
    public function findOneByEmail(string $email): ?User
    {
        return $this->findOneBy(['email' => $email]);
    }
}