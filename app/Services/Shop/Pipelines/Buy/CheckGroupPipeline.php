<?php


namespace App\Services\Shop\Pipelines\Buy;


use App\DataObjects\Shop\PipelineObject;
use App\Entity\Site\UserGroup;
use App\Exceptions\Exception;
use Closure;

class CheckGroupPipeline
{
    public function handle(PipelineObject $po, Closure $next)
    {
        $groups = $po->getProduct()->getForArray();
        if (!empty($groups)) {
            $server = $po->getServer();
            $group = $po->getUser()->getGroups()->filter(function (UserGroup $userGroup) use ($groups, $server) {
                return $userGroup->getServer()->getId() == $server->getId() && in_array($userGroup->getGroup()->getName(), $groups);
            })->first();

            if (!($group instanceof UserGroup)) {
                throw new Exception('Вы не можете купить этот товар, т.к. не состоите в разрешенной группе!');
            }
        }

        return $next($po);
    }
}