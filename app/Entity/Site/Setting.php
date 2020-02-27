<?php


namespace App\Entity\Site;


use App\Services\Settings\DataType;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="pr_settings")
 * @ORM\HasLifecycleCallbacks
 */
class Setting
{
    /**
     * @var int
     *
     * @ORM\Id
     * @ORM\Column(name="id", type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="`key`", type="string", length=255, unique=true, nullable=false)
     */
    private $key;

    /**
     * @var string|null
     *
     * @ORM\Column(name="value", type="text", unique=false, nullable=true)
     */
    private $value;

    /**
     * @var \DateTimeImmutable|null
     *
     * @ORM\Column(name="updated_at", type="datetime_immutable", nullable=true)
     */
    private $updatedAt;

    /**
     * Setting constructor.
     *
     * @param string $key
     * @param $value
     */
    public function __construct(string $key, $value)
    {
        $this->key = $key;
        $this->value = $value;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getKey(): string
    {
        return $this->key;
    }

    /**
     * @param string $key
     */
    public function setKey(string $key): void
    {
        $this->key = $key;
    }

    /**
     * @param string|null $castTo
     * @return mixed
     */
    public function getValue(?string $castTo = null)
    {
        switch ($castTo) {
            case DataType::BOOL:
                return (bool)$this->value;
            case DataType::INT:
                return (int)$this->value;
            case DataType::FLOAT:
                return (float)$this->value;
            case DataType::JSON:
                return json_decode($this->value, true);
            case DataType::SERIALIZED:
                return unserialize($this->value);
            default:
                return $this->value;
        }
    }

    /**
     * @param string|null $value
     */
    public function setValue(?string $value): void
    {
        $this->value = $value;
    }

    /**
     * @return \DateTimeImmutable|null
     */
    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    /**
     * @ORM\PreUpdate
     * @throws \Exception
     */
    public function preUpdate(): void
    {
        $this->updatedAt = new \DateTimeImmutable();
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->value;
    }
}