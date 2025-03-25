<?php

declare(strict_types=1);

namespace Gwo\AppsRecruitmentTask\Domain\Document\User;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Gwo\AppsRecruitmentTask\Util\StringId;

#[ODM\Document(collection: 'User')]
final readonly class User
{
    #[ODM\Id(strategy: "AUTO")]
    private StringId $id;

    #[ODM\Field(type: "string", nullable: false)]
    private string $name;

    #[ODM\Field(type: "string", nullable: false)]
    private UserRole $role;

    public function __construct(
        StringId $id,
        string $name,
        UserRole $role
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->role = $role;
    }

    public function getId(): StringId
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getRole(): UserRole
    {
        return $this->role;
    }
}