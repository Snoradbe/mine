<?php


namespace App\Console\Cron;


use App\Entity\Site\Log;
use App\Entity\Site\TopLastVotes;
use App\Entity\Site\UserNotification;
use App\Repository\Site\Log\LogRepository;
use App\Repository\Site\TopLastVotes\TopLastVotesRepository;
use App\Repository\Site\User\UserRepository;
use App\Repository\Site\UserNotification\UserNotificationRepository;
use App\Services\Settings\DataType;
use Illuminate\Console\Command;

class Votes extends Command
{
    protected $signature = 'rm:cron:votes';

    private $userRepository;

    private $topLastVotesRepository;

    private $logRepository;

    private $userNotificationRepository;

    public function __construct(
        UserRepository $userRepository,
        TopLastVotesRepository $topLastVotesRepository,
        LogRepository $logRepository,
        UserNotificationRepository $userNotificationRepository)
    {
        parent::__construct();

        $this->userRepository = $userRepository;
        $this->topLastVotesRepository = $topLastVotesRepository;
        $this->logRepository = $logRepository;
        $this->userNotificationRepository = $userNotificationRepository;
    }

    public function handle()
    {
        $settings = settings('tops.base', DataType::JSON, []);
        $rewards = $settings['month_rewards'] ?? [];
        $giveMax = $settings['month_give_max'] ?? [];

        $top = $this->userRepository->getTopVotes(count($rewards));

        $prev = [0, 0];

        foreach ($top as $i => $user)
        {
            $reward = $rewards[$i];
            if ($giveMax && $prev[1] == $user->getVotes()) {
                $reward = $rewards[$prev[0]];
            }

            $user->depositMoney($reward);
            $this->logRepository->create(new Log(
                $user,
                null,
                1015,
                'cron',
                [
                    'position' => ($i + 1),
                ],
                null,
                $reward,
                'rub'
            ));
            $this->topLastVotesRepository->create(new TopLastVotes(
                $user,
                $user->getVotes(),
                ($i + 1),
                $reward
            ));
            $this->userNotificationRepository->create(new UserNotification(
                $user,
                printf('Поздравляем! Вы заняли %d место в топе голосующих за этот месяц и получили %d руб.',
                    ($i + 1), $reward)
            ));

            $prev[0] = $i;
            $prev[1] = $user->getVotes();
        }

        $this->userRepository->clearVotes();
        //TODO: discord-log - топ был очищен, победители: ...
    }
}