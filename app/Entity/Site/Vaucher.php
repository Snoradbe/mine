<?php


namespace App\Entity\Site;


use App\Exceptions\Exception;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

/**
 * Vaucher
 *
 * @ORM\Table(name="pr_vauchers", uniqueConstraints={@ORM\UniqueConstraint(name="code", columns={"code"})})
 * @ORM\Entity
 */
class Vaucher
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false, options={"unsigned"=true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="code", type="string", length=100, nullable=false)
     */
    private $code;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", length=36, nullable=false)
     */
    private $type;

    /**
     * @var string
     *
     * @ORM\Column(name="value", type="text", nullable=false)
     */
    private $value;

    /**
     * @var int|null
     *
     * @ORM\Column(name="amount", type="smallint", nullable=true)
     */
    private $amount;

    /**
     * @var DateTimeImmutable
     *
     * @ORM\Column(name="start", type="datetime_immutable", nullable=false)
     */
    private $start;

    /**
     * @var DateTimeImmutable|null
     *
     * @ORM\Column(name="end", type="datetime_immutable", nullable=true)
     */
    private $end;

    /**
     * @var string|null
     *
     * @ORM\Column(name="message", type="text", nullable=true)
     */
    private $message;

    /**
     * @var User|null
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Site\User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="user_id", referencedColumnName="user_id")
     * })
     */
    private $user;

    /**
     * @var array
     */
    private $valueArray;

    /**
     * Vaucher constructor.
     *
     * @param string $code
     * @param string $type
     * @param array $value
     * @param int|null $amount
     * @param DateTimeImmutable $start
     * @param DateTimeImmutable|null $end
     * @param string|null $message
     * @param User|null $user
     * @throws Exception
     */
    public function __construct(
        string $code,
        string $type,
        array $value,
        ?int $amount,
        DateTimeImmutable $start,
        ?DateTimeImmutable $end = null,
        ?string $message = null,
        ?User $user = null)
    {
        $this->code = $code;
        $this->type = $type;
        $this->setValue($value);
        $this->amount = $amount;
        $this->start = $start;
        $this->end = $end;
        $this->message = $message;
        $this->user = $user;
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
    public function getCode(): string
    {
        return $this->code;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType(string $type): void
    {
        $this->type = $type;
    }

    /**
     * @return string
     */
    public function getValue(): string
    {
        return $this->value;
    }

    /**
     * @param array $data
     * @throws Exception
     */
    public function setValue(array $data): void
    {
        if (empty($data)) {
            throw new Exception('Data is empty!');
        }

        $this->value = json_encode($data);
        $this->valueArray = $data;
    }

    /**
     * @return array
     */
    public function getValueArray(): array
    {
        if (is_null($this->valueArray)) {
            $this->valueArray = (array) json_decode($this->value, true);
        }

        return $this->valueArray;
    }

    /**
     * @return int|null
     */
    public function getAmount(): ?int
    {
        return $this->amount;
    }

    /**
     * @param int|null $amount
     */
    public function setAmount(?int $amount): void
    {
        $this->amount = $amount;
    }

    /**
     * @return DateTimeImmutable
     */
    public function getStart(): DateTimeImmutable
    {
        return $this->start;
    }

    /**
     * @param DateTimeImmutable $start
     */
    public function setStart(DateTimeImmutable $start): void
    {
        $this->start = $start;
    }

    /**
     * @return DateTimeImmutable|null
     */
    public function getEnd(): ?DateTimeImmutable
    {
        return $this->end;
    }

    /**
     * @param DateTimeImmutable|null $end
     */
    public function setEnd(?DateTimeImmutable $end): void
    {
        $this->end = $end;
    }

    /**
     * @return string|null
     */
    public function getMessage(): ?string
    {
        return $this->message;
    }

    /**
     * @param string|null $message
     */
    public function setMessage(?string $message): void
    {
        $this->message = $message;
    }

    /**
     * @return User|null
     */
    public function getUser(): ?User
    {
        return $this->user;
    }

    /**
     * @param User|null $user
     */
    public function setUser(?User $user): void
    {
        $this->user = $user;
    }

    /**
     * @return void
     */
    public function activate(): void
    {
        if ($this->amount > 0) {
            --$this->amount;
        }
    }

    /**
     * @return bool
     */
    public function isActive(): bool
    {
        return
            ($this->amount == -1 || $this->amount > 0)
            &&
            (is_null($this->end) || $this->end->getTimestamp() > time());
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'code' => $this->code,
            'type' => $this->type,
            'reward' => $this->getValueArray(),
            'message' => $this->message,
            'amount' => $this->amount,
            'start' => $this->start->format('d.m.Y H:i'),
            'end' => is_null($this->end) ? null : $this->end->format('d.m.Y H:i'),
            'for' => is_null($this->user) ? null : $this->user->toSimpleArray()
        ];
    }
}