<?php


namespace App\Handlers\Client\Cabinet;


use App\Entity\Site\User;
use App\Exceptions\Exception;
use App\Repository\Site\Log\LogRepository;
use Illuminate\Pagination\LengthAwarePaginator;

class LogsHandler
{
    private $types = [
        'actions' => [
            3, 4, 5, 6, 9, 11, 12, 13
        ],
        'replenishments' => [
            12, 13
        ],
        'costs' => [
            5, 6
        ]
    ];

    private $logRepository;

    public function __construct(LogRepository $logRepository)
    {
        $this->logRepository = $logRepository;
    }

    private function checkType(string $type): void
    {
        if (!isset($this->types[$type])) {
            throw new Exception('Тип ' . $type . ' не найден!');
        }
    }

    public function getLogs(User $user, string $type, int $page): LengthAwarePaginator
    {
        $this->checkType($type);

        return $this->logRepository->findByUser($user, $this->types[$type], $page);
    }
}