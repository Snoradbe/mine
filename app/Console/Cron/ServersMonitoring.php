<?php


namespace App\Console\Cron;


use App\Repository\Site\Server\ServerRepository;
use App\Services\Game\MineQuery\Query;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class ServersMonitoring extends Command
{
    protected $signature = 'rm:cron:monitoring';

    private $serverRepository;
    private $mineQuery;

    public function __construct(ServerRepository $serverRepository, Query $mineQuery)
    {
        parent::__construct();

        $this->serverRepository = $serverRepository;
        $this->mineQuery = $mineQuery;
    }

    public function handle()
    {
        $storage = Storage::disk('local');

        $date = date('Y-m-d H:i');

        //$dateTime = new \DateTimeImmutable($date . ':00');

        $online = 0;

        $servers = $this->serverRepository->getAll(false);

        foreach ($servers as $server)
        {
            try {
                $this->mineQuery->Connect($server->getIp(), $server->getPort());

                $info = $this->mineQuery->GetInfo();

                $currentOnline = (int) $info['Players'];
                $online += $currentOnline;

                $prevOnline = $storage->exists('monitoring/serv_' . $server->getId())
                    ? (int) $storage->get('monitoring/serv_' . $server->getId())
                    : -1;

                if($currentOnline !== $prevOnline) {
                    continue;
                }

                $storage->put('monitoring/serv_' . $server->getId() . '.txt', $currentOnline);

                /*$this->serverOnlineRepository->create(new ServerOnline(
                    $server->getId(),
                    $dateTime,
                    $currentOnline
                ));*/
            } catch (\Exception $exception) {
                $storage->put('monitoring/serv_' . $server->getId() . '.txt', -1);
            }
        }

        /*$this->serverOnlineRepository->create(new ServerOnline(
            null,
            $dateTime,
            $online
        ));*/
    }
}