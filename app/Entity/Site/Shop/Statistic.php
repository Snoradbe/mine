<?php


namespace App\Entity\Site\Shop;


use App\Entity\Site\User;
use Doctrine\ORM\Mapping as ORM;

/**
 * Shop\Statistic
 *
 * @ORM\Table(name="pr_shop_statistics", indexes={@ORM\Index(name="pr_shop_statistics_user_id_foreign", columns={"user_id"}), @ORM\Index(name="pr_shop_statistics_product_id_foreign", columns={"product_id"})})
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 */
class Statistic
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
     * @var int
     *
     * @ORM\Column(name="amount", type="integer", nullable=false, options={"unsigned"=true})
     */
    private $amount;

    /**
     * @var int
     *
     * @ORM\Column(name="price", type="integer", nullable=false, options={"unsigned"=true})
     */
    private $price;

    /**
     * @var string
     *
     * @ORM\Column(name="valute", type="string", length=20, nullable=false)
     */
    private $valute;

    /**
     * @var string
     *
     * @ORM\Column(name="day_date", type="string", length=10, nullable=false, options={"comment"="2019-03-18"})
     */
    private $dayDate;

    /**
     * @var \DateTimeImmutable
     *
     * @ORM\Column(name="created_at", type="datetime_immutable", nullable=false, options={"default"="CURRENT_TIMESTAMP"})
     */
    private $createdAt = 'CURRENT_TIMESTAMP';

    /**
     * @var Product
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Site\Shop\Product")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="product_id", referencedColumnName="id")
     * })
     */
    private $product;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Site\User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="user_id", referencedColumnName="user_id")
     * })
     */
    private $user;

    /**
     * Statistic constructor.
     *
     * @param User $user
     * @param Product $product
     * @param int $amount
     * @param int $price
     * @param string $valute
     */
    public function __construct(User $user, Product $product, int $amount, int $price, string $valute)
    {
        $this->user = $user;
        $this->product = $product;
        $this->amount = $amount;
        $this->price = $price;
        $this->valute = $valute;
        $this->dayDate = date('Y-m-d');
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * @return Product
     */
    public function getProduct(): Product
    {
        return $this->product;
    }

    /**
     * @return int
     */
    public function getAmount(): int
    {
        return $this->amount;
    }

    /**
     * @return int
     */
    public function getPrice(): int
    {
        return $this->price;
    }

    /**
     * @return string
     */
    public function getValute(): string
    {
        return $this->valute;
    }

    /**
     * @return string
     */
    public function getDayDate(): string
    {
        return $this->dayDate;
    }

    /**
     * @return \DateTimeImmutable
     */
    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    /**
     * @ORM\PrePersist
     * @throws \Exception
     */
    public function prePersist(): void
    {
        $this->createdAt = new \DateTimeImmutable();
    }
}