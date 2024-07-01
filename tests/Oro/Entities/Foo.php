<?php
declare(strict_types=1);

namespace Oro\Entities;

use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\Table;

#[Entity]
#[Table(name: 'test_foo')]
class Foo
{
    #[Id]
    #[Column(name: 'id', type: 'integer')]
    #[GeneratedValue(strategy: 'AUTO')]
    protected int $id;

    #[Column(name: "name", type: "string", length: 255)]
    protected string $name;

    #[Column(name: "created_at", type: "datetime", nullable: true)]
    protected \DateTime $createdAt;

    #[Column(name: "budget", type: "float", nullable: true)]
    protected float $budget;

    #[Column(name: "code", type: "string", length: 255)]
    protected string $code;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setBudget(float $budget): void
    {
        $this->budget = $budget;
    }

    public function getBudget(): ?float
    {
        return $this->budget;
    }

    public function setCreatedAt(\DateTime $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    public function getCreatedAt(): ?\DateTime
    {
        return $this->createdAt;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): void
    {
        $this->code = $code;
    }
}
