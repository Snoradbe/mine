<?php


namespace App\Handlers\Api\Vote;


use App\Entity\Site\User;
use App\Entity\Site\VoteLog;
use App\Events\Api\Vote\VoteEvent;
use App\Repository\Site\User\UserRepository;
use App\Repository\Site\VoteLog\VoteLogRepository;
use App\Services\Voting\RewardHandlers\CoinsHandler;
use App\Services\Voting\RewardHandlers\MoneyHandler;
use App\Services\Voting\Tops\Pool;
use Illuminate\Http\Request;

class VoteHandler
{
    private $request;

    private $pool;

    private $userRepository;

    private $voteLogRepository;

    private $moneyReward;

    private $coinsReward;

    public function __construct(
        Request $request,
        Pool $pool,
        UserRepository $userRepository,
        VoteLogRepository $voteLogRepository,
        MoneyHandler $moneyReward,
        CoinsHandler $coinsReward)
    {
        $this->request = $request;
        $this->pool = $pool;
        $this->userRepository = $userRepository;
        $this->voteLogRepository = $voteLogRepository;

        $this->moneyReward = $moneyReward;
        $this->coinsReward = $coinsReward;
    }

    public function handle(string $top): void
    {
        $top = strtolower($top);

        $top = $this->pool->retrieveByName($top);
        if (is_null($top)) {
            die('Топ не найден!');
        }

        $top->init($this->request->all());

        if (!$top->checkSign($this->request->all())) {
            $top->error('Неверная подпись!');
        }

        $user = $this->userRepository->findByName($top->getUserName());
        if (is_null($user)) {
            $top->error('Игрок не найден!');
        }

        $votesToday = $this->voteLogRepository->getCountToday($user);

        //Если сегодня еще не проголосовал во всех топах, то выдаем плюшки. Иначе просто плюсик в карму
        if ($votesToday < count($this->pool->all())) {
            foreach ($top->getRewards() as $reward => $rewardData)
            {
                $this->giveReward($user, $reward, $rewardData);
            }

            $user->addVote();
            $this->userRepository->update($user);
            $this->voteLogRepository->create(new VoteLog($user, $top->getName()));
        } else {
            $user->addTotalVote();
            $this->userRepository->update($user);
        }

        event(new VoteEvent($user, $top));

        $top->success();
    }

    private function giveReward(User $user, string $reward, array $data): void
    {
        switch ($reward)
        {
            case 'money': $this->moneyReward->handle($user, $data); break;
            case 'coins': $this->coinsReward->handle($user, $data); break;
        }
    }
}