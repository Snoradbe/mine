<?php


namespace App\Services\Shop\Pipelines\Buy;


use App\DataObjects\Shop\PipelineObject;
use App\Exceptions\Exception;
use App\Services\Discounts\Discounts;
use Closure;

class DiscountPipeline
{
    public function handle(PipelineObject $po, Closure $next)
    {
        $discount = $po->getProduct()->getRealDiscount();
        /*if(
            $po->getProduct()->getDiscount() > 0 &&
            (is_null($po->getProduct()->getDiscountTime()) ||
                $po->getProduct()->getDiscountTime()->getTimestamp() > time())
        ) {
            $discount = $po->getProduct()->getDiscount();
        }*/

        $massDiscount = Discounts::getInstance()->getDiscount($po->getServer(), 'shop', null, null);
        $discount = $massDiscount > $discount ? $massDiscount : $discount;

        if($discount > 0 && $discount < 100) {
            $po->setDiscount($discount);
            $sum = $po->getResultSum();
            $sum = Discounts::getPriceWithDiscount($sum, $discount);

            if($sum < 1) {
                throw new Exception('Сумма с учетом скидки меньше 1!');
            }

            $po->setResultSum($sum);
        }

        return $next($po);
    }
}