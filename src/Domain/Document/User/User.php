<?php

declare(strict_types=1);

namespace Gwo\AppsRecruitmentTask\Domain\Document\User;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Gwo\AppsRecruitmentTask\Domain\Enum\CollectionNameEnum;
use Gwo\AppsRecruitmentTask\Domain\Enum\UserRole;
use Gwo\AppsRecruitmentTask\Util\StringId;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ODM\Document(collection: CollectionNameEnum::USER->value)]
final class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ODM\Id(strategy: 'AUTO')]
    private StringId $id;

    #[ODM\Field(type: 'string', nullable: false)]
    private string $name;

    #[ODM\Field(type: 'string', nullable: false)]
    private UserRole $role;

    #[ODM\Field(type: 'string', nullable: true)]
    private ?string $password = null;

    public function __construct(
        StringId $id,
        string $name,
        UserRole $role,
        ?string $password = null,
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->role = $role;
        $this->password = $password;
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

    public function getRoles(): array
    {
        return [$this->role->value];
    }

    public function getUserIdentifier(): string
    {
        return $this->name;
    }

    public function eraseCredentials(): void
    {
        $this->password = null;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }
}
