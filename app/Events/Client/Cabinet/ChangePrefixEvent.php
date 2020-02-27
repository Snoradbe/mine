<?php


namespace App\Events\Client\Cabinet;


use App\Entity\Site\Server;
use App\Entity\Site\User;
use App\Events\Client\ClientEvent;
use App\Events\Client\EventWithServer;
use App\Events\Event;
use App\Services\Cabinet\Prefix\PrefixSuffix;

class ChangePrefixEvent extends ClientEvent implements Event
{
    use EventWithServer;

    /**
     * @var PrefixSuffix
     */
    private $prefixSuffix;

    /**
     * ChangePrefixEvent constructor.
     * @param User $user
     * @param Server $server
     * @param PrefixSuffix $prefixSuffix
     */
    public function __construct(User $user, Server $server, PrefixSuffix $prefixSuffix)
    {
        parent::__construct($user);

        $this->server = $server;
        $this->prefixSuffix = $prefixSuffix;
    }

    /**
     * @return PrefixSuffix
     */
    public function getPrefixSuffix(): PrefixSuffix
    {
        return $this->prefixSuffix;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'prefix_suffix' => $this->prefixSuffix->toArray(),
            'prefix_format' => $this->prefixSuffix->prefixToPermissionFormat(),
            'suffix_format' => $this->prefixSuffix->suffixToPermissionFormat(),
        ];
    }
}