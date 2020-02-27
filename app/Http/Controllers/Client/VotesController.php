<?php


namespace App\Http\Controllers\Client;


use App\Entity\Site\User;
use App\Http\Controllers\Controller;
use App\Repository\Site\User\UserRepository;
use App\Repository\Site\VoteLog\VoteLogRepository;
use App\Services\Auth\Auth;
use App\Services\Response\JsonResponse;
use App\Services\Settings\DataType;

class VotesController extends Controller
{
    public function load(UserRepository $userRepository, VoteLogRepository $voteLogRepository)
    {
        $base = settings('tops.base', DataType::JSON, []);
        $tops = settings('tops.tops', DataType::JSON, []);

        $day = (int) date('d');
        $sevenDays = $day <= 7;

        $topsData = [];
        foreach ($tops as $top => $data)
        {
            if ($data['enabled']) {
                $topsData[$top] = $data;
                $rewards = [];
                foreach ($data['rewards'] as $rewardType => $rewardData)
                {
                    $reward = $rewardData;
                    if ($sevenDays && isset($rewardData['7bonus']) && $rewardData['7bonus']) {
                        if (isset($rewardData['amount'])) {
                            $reward['amount'] *= 2;
                        }
                    }
                    $rewards[$rewardType] = $reward;
                }
                $topsData[$top]['rewards'] = $rewards;
                unset($topsData['secret']);
            }
        }

        $last = $voteLogRepository->getUserLast(Auth::getUser());
        if (!is_null($last)) {
            $last = $last->toArray();
        }

        return new JsonResponse([
            'tops' => $topsData,
            'month_rewards' => $base['month_rewards'],
            'top' => $userRepository->getTopVotes(),
            'last' => $last
        ]);
    }
}