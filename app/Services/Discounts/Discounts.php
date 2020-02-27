<?php


namespace App\Services\Discounts;


use App\Entity\Site\Discount;
use App\Entity\Site\Group;
use App\Entity\Site\Server;
use App\Exceptions\Exception;
use App\Repository\Site\Discount\DiscountRepository;

class Discounts
{
    /**
     * @var Discounts
     */
    private static $instance;

    /**
     * @var Discount[]
     */
    private $discounts = [];

    /**
     * @return Discounts
     */
    public static function getInstance(): Discounts
    {
        if (is_null(static::$instance)) {
            static::$instance = new self(app()->make(DiscountRepository::class));
        }

        return static::$instance;
    }

    /**
     * @param int $base
     * @param int $discount
     * @return int
     */
    public static function getPriceWithDiscount(int $base, int $discount): int
    {
        if ($discount > 0) {
            return ceil($base - $base * ($discount / 100));
        }

        return $base;
    }

    /**
     * Discounts constructor.
     * @param DiscountRepository $discountRepository
     */
    private function __construct(DiscountRepository $discountRepository)
    {
        $this->discounts = $discountRepository->getAll();
    }
    private function __clone(){}
    private function __wakeup(){}

    /**
     * @param Server|null $server
     * @param string $module - модуль: cabinet|shop|unban...
     * @param string|null $type - тип: null|group...
     * @param mixed|null $data - различное значение (для type=group: Group object)
     * @return int
     * @throws Exception
     */
    public function getDiscount(?Server $server, string $module, ?string $type, $data): int
    {
        $discounts = $this->getServerDiscounts($server);
        $discount = $this->getAllModulesDiscount($discounts, $module);

        switch ($type)
        {
            case 'group':
                $disc = $this->getGroupDiscount($discounts, $data);
                if ($disc > $discount) {
                    $discount = $disc;
                }
                break;
        }

        return $discount;
    }

    /**
     * @param Server|null $server
     * @return array
     */
    private function getServerDiscounts(?Server $server): array
    {
        if (is_null($server)) {
            return array_filter($this->discounts, function (Discount $discount) {
                return is_null($discount->getServer());
            });
        } else {
            return array_filter($this->discounts, function (Discount $discount) use ($server) {
                return is_null($discount->getServer()) || $discount->getServer() === $server;
            });
        }
    }

    /**
     * @param Discount[] $discounts
     * @param string $module
     * @return int
     */
    private function getAllModulesDiscount(array $discounts, string $module): int
    {
        $disc = 0;
        foreach ($discounts as $discount)
        {
            switch ($discount->getType())
            {
                case 'all':
                case 'module_' . $module:
                    if ($disc < $discount->getDiscount()) {
                        $disc = $discount->getDiscount();
                    }
            }
        }

        return $disc;
    }

    /**
     * @param Discount[] $discounts
     * @param $group
     * @return int
     * @throws Exception
     */
    private function getGroupDiscount(array $discounts, $group): int
    {
        if (!($group instanceof Group)) {
            throw new Exception('$group is not instance of Group!');
        }

        $disc = 0;

        foreach ($discounts as $discount)
        {
            if (
                (
                    $discount->getType() == 'groups_all'
                    ||
                    ($discount->getType() == 'groups_primary' && $group->isPrimary())
                    ||
                    ($discount->getType() == 'groups_other' && !$group->isPrimary())
                )
                &&
                $disc < $discount->getDiscount()
            ) {
                $disc = $discount->getDiscount();
            }
        }

        return $disc;
    }
}