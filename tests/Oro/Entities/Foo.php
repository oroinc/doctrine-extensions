<?php
declare(strict_types=1);

namespace Oro\Entities;

/**
 * @Entity
 * @Table(name="test_foo")
 */
class Foo
{
    /**
     * @var int
     *
     * @Id
     * @Column(type="integer", name="id")
     * @GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     *
     * @Column(name="name", type="string", length=255)
     */
    protected $name;

    /**
     * @var \DateTime $createdAt
     *
     * @Column(name="created_at", type="datetime", nullable=true)
     */
    protected $createdAt;

    /**
     * @var float
     *
     * @Column(name="budget", type="float", nullable=true)
     */
    protected $budget;

    /**
     * @var string
     *
     * @Column(name="code", type="string", length=255)
     */
    protected $code;

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
