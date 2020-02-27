<?php


namespace App\Handlers\Client\Applications;


use App\Entity\Site\Application;
use App\Entity\Site\Server;
use App\Entity\Site\User;
use App\Events\Client\Applications\SendApplicationEvent;
use App\Exceptions\Exception;
use App\Repository\Site\Application\ApplicationRepository;
use App\Repository\Site\Server\ServerRepository;
use App\Services\Settings\DataType;

class SendHandler
{
    private $applicationRepository;

    private $serverRepository;

    public function __construct(ApplicationRepository $applicationRepository, ServerRepository $serverRepository)
    {
        $this->applicationRepository = $applicationRepository;
        $this->serverRepository = $serverRepository;
    }

    private function getServer(int $id): Server
    {
        $server = $this->serverRepository->find($id);
        if (is_null($server)) {
            throw new Exception('Сервер не найден!');
        }

        return $server;
    }

    private function getLastApplication(User $user): ?Application
    {
        return $this->applicationRepository->findLast($user);
    }

    private function getSettings(string $group, int $server): array
    {
        $settings = settings('applications', DataType::JSON, []);
        $cooldown = $settings['cooldown'];
        $minLevel = $settings['min_level'];
        $settings = $settings['statuses'];

        if (!isset($settings[$group])) {
            throw new Exception('На эту должность нельзя подать заявку!');
        }

        if (!isset($settings[$group]['enabled'][$server]) || !$settings[$group]['enabled'][$server]) {
            throw new Exception('На этот сервер нельзя подать заявку!');
        }

        $settings = $settings[$group];
        $settings['server'] = $settings['server'][$server] ?? [];

        return [$cooldown, $settings, $minLevel];
    }

    private function parseFormData(array $settings, array $answers, array $serverAnswers): array
    {
        $result = [
            'answers' => [],
            'server' => []
        ];

        foreach ($settings['form'] as $i => $question)
        {
            $answer = trim(strip_tags($answers[$i]));
            if ($answer == '') {
                throw new Exception("Вы не ответили на вопрос '$question'!");
            }
            $result['answers'][$question] = $answer;
        }

        foreach ($settings['server'] as $i => $question)
        {
            $answer = trim(strip_tags($serverAnswers[$i]));
            if ($answer == '') {
                throw new Exception("Вы не ответили на серверный вопрос '$question''!");
            }
            $result['server'][$question] = $answer;
        }

        return $result;
    }

    public function handle(User $user, string $group, string $serverId, array $answers, array $serverAnswers): void
    {
        if ($user->inTeam()) {
            throw new Exception('Вы уже в команде администрации, поэтому не можете подать заявку!');
        }

        $server = $this->getServer($serverId);

        [$cooldown, $settings, $minLevel] = $this->getSettings($group, $server->getId());

        if (!$user->hasLevel($minLevel)) {
            throw new Exception('Подавать заявки можно с уровня: ' . $minLevel);
        }

        if (count($settings['form']) != count($answers) || count($settings['server']) != count($serverAnswers)) {
            throw new Exception('Вы ответили не на все вопросы!');
        }

        $lastApplication = $this->getLastApplication($user);

        if (!is_null($lastApplication) && !$lastApplication->canAgain($cooldown)) {
            throw new Exception("Вы недавно подавали заявку, возможно еще не прошло $cooldown дней");
        }

        $application = new Application(
            $server,
            $user,
            $this->parseFormData($settings, $answers, $serverAnswers),
            $group
        );

        $this->applicationRepository->create($application);

        event(new SendApplicationEvent($user, $server, $application));
    }
}