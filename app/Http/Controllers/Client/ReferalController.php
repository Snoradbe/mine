<?php


namespace App\Http\Controllers\Client;


use App\Entity\Site\ReferalLog;
use App\Entity\Site\User;
use App\Http\Controllers\Controller;
use App\Repository\Site\User\UserRepository;
use App\Services\Auth\Auth;
use App\Services\Response\JsonResponse;
use App\Services\Settings\DataType;

class ReferalController extends Controller
{
    public function load(UserRepository $userRepository)
    {
        $page = abs((int) request('page', 1));

        $referals = $userRepository->getReferals(Auth::getUser(), $page);

        return new JsonResponse([
            'referals' => array_map(function (User $user) {
                //count($user->getReferalSteps());
                return [
                    'id' => $user->getId(),
                    'name' => $user->getName(),
                    'reg_date' => $user->getRegDate(),
                    'level' => $user->getLevel(),
                    'steps' => array_map(function (ReferalLog $log) {
                        return $log->getType();
                    }, $user->getReferalSteps()->toArray())
                ];
            }, $referals->all()),
            'settings' => settings('referal.handlers', DataType::JSON, [])
        ]);
    }
}