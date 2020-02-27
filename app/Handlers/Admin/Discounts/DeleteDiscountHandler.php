<?php


namespace App\Handlers\Admin\Discounts;


use App\Entity\Site\Discount;
use App\Entity\Site\User;
use App\Events\Admin\Discounts\DeleteDiscountEvent;
use App\Exceptions\Exception;
use App\Repository\Site\Discount\DiscountRepository;

class DeleteDiscountHandler
{
    private $discountRepository;

    public function __construct(DiscountRepository $discountRepository)
    {
        $this->discountRepository = $discountRepository;
    }

    private function getDiscount(int $id): Discount
    {
        $discount = $this->discountRepository->find($id);
        if (is_null($discount)) {
            throw new Exception('Скидка не найдена!');
        }

        return $discount;
    }

    public function handle(User $admin, int $id): void
    {
        $discount = $this->getDiscount($id);

        $this->discountRepository->delete($discount);

        event(new DeleteDiscountEvent($admin, $discount));
    }
}