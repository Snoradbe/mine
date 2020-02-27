<?php


namespace App\Services\Game\HawkLogs;


class HawkSearch
{
    public const MAX_RANGE = 300;

    /**
     * @var array
     */
    private $actions = [];

    /**
     * @var array
     */
    private $users = [];

    /**
     * @var array
     */
    private $ignoredUsers = [];

    /**
     * @var string|null
     */
    private $data;

    /**
     * @var array
     */
    private $excludedData = [];

    /**
     * @var string|null
     */
    private $dateFrom;

    /**
     * @var string|null
     */
    private $dateTo;

    /**
     * @var int|null
     */
    private $x;

    /**
     * @var int|null
     */
    private $y;

    /**
     * @var int|null
     */
    private $z;

    /**
     * @var int
     */
    private $range = 30;

    /**
     * @var array
     */
    private $worlds = [];

    /**
     * @var array
     */
    private $ignoredWorlds = [];

    /**
     * HawkSearch constructor.
     * @param array $actions
     * @param array $users
     * @param string|null $data
     * @param array $excludedData
     * @param string|null $dateFrom
     * @param string|null $dateTo
     * @param int|null $x
     * @param int|null $y
     * @param int|null $z
     * @param int|null $range
     * @param array $worlds
     */
    public function __construct(
        array $actions = [],
        array $users = [],
        ?string $data = null,
        array $excludedData = [],
        ?string $dateFrom = null,
        ?string $dateTo = null,
        ?int $x = null,
        ?int $y = null,
        ?int $z = null,
        ?int $range = null,
        array $worlds = [])
    {
        $this->actions = $actions;
        $this->parseUsers($users);
        $this->parseData($data, $excludedData);
        if (!empty($data) || $data == '0') {
            $this->data = $data;
        }
        if (!empty($dateFrom)) {
            $this->dateFrom = $dateFrom;
        }
        if (!empty($dateTo)) {
            $this->dateTo = $dateTo;
        }
        $this->x = $x;
        $this->y = $y;
        $this->z = $z;
        if (!is_null($range) && $range > 0 && $range < static::MAX_RANGE) {
            $this->range = $range;
        }
        $this->parseWorlds($worlds);
    }

    private function parseData(?string $findingData, array $excludedData): void
    {
        $findingData = trim($findingData);

        foreach ($excludedData as $data)
        {
            $data = trim($data);
            if ((!empty($data) || $data == '0') && $findingData != $data && !in_array($data, $this->excludedData)) {
                $this->excludedData[] = $data;
            }
        }

        if (!is_null($findingData) && !in_array($findingData, $this->excludedData)) {
            $this->data = $findingData;
        }
    }

    /**
     * @param array $worlds
     */
    private function parseWorlds(array $worlds): void
    {
        foreach ($worlds as $world)
        {
            $world = strtolower($world);

            $expl = explode('!', $world);
            if (count($expl) > 1) { //Если мир начинается на !, то исключаем из поиска
                $this->addIgnoredWorld($expl[1]);
            } else {
                $this->addWorld($world);
            }
        }
    }

    /**
     * @param string $world
     */
    private function addWorld(string $world): void
    {
        if (!in_array($world, $this->worlds) && !in_array($world, $this->ignoredWorlds)) {
            $this->worlds[] = $world;
        }
    }

    /**
     * @param string $world
     */
    private function addIgnoredWorld(string $world): void
    {
        if (!in_array($world, $this->ignoredWorlds) && !in_array($world, $this->worlds)) {
            $this->ignoredWorlds[] = $world;
        }
    }

    /**
     * @param array $users
     */
    private function parseUsers(array $users): void
    {
        foreach ($users as $user)
        {
            $user = strtolower($user);

            $expl = explode('!', $user);
            if (count($expl) > 1) { //Если ник игрока начинается на !, то исключаем из поиска
                $this->addIgnoredUser($expl[1]);
            } else {
                $this->addUser($user);
            }
        }
    }

    /**
     * @param string $user
     */
    private function addUser(string $user): void
    {
        if (!in_array($user, $this->users) && !in_array($user, $this->ignoredUsers)) {
            $this->users[] = $user;
        }
    }

    /**
     * @param string $user
     */
    private function addIgnoredUser(string $user): void
    {
        if (!in_array($user, $this->ignoredUsers) && !in_array($user, $this->users)) {
            $this->ignoredUsers[] = $user;
        }
    }

    /**
     * @return array
     */
    public function getActions(): array
    {
        return $this->actions;
    }

    /**
     * @return array
     */
    public function getUsers(): array
    {
        return $this->users;
    }

    /**
     * @return array
     */
    public function getIgnoredUsers(): array
    {
        return $this->ignoredUsers;
    }

    /**
     * @return string|null
     */
    public function getData(): ?string
    {
        return $this->data;
    }

    /**
     * @return array
     */
    public function getExcludedData(): array
    {
        return $this->excludedData;
    }

    /**
     * @return string|null
     */
    public function getDateFrom(): ?string
    {
        return $this->dateFrom;
    }

    /**
     * @return string|null
     */
    public function getDateTo(): ?string
    {
        return $this->dateTo;
    }

    /**
     * @return int|null
     */
    public function getX(): ?int
    {
        return $this->x;
    }

    /**
     * @return array
     */
    public function getRangedX(): array
    {
        return [$this->x - $this->range, $this->x + $this->range];
    }

    /**
     * @return int|null
     */
    public function getY(): ?int
    {
        return $this->y;
    }

    /**
     * @return array
     */
    public function getRangedY(): array
    {
        return [$this->y - $this->range, $this->y + $this->range];
    }

    /**
     * @return int|null
     */
    public function getZ(): ?int
    {
        return $this->z;
    }

    /**
     * @return array
     */
    public function getRangedZ(): array
    {
        return [$this->z - $this->range, $this->z + $this->range];
    }

    /**
     * @return int
     */
    public function getRange(): int
    {
        return $this->range;
    }

    /**
     * @return array
     */
    public function getWorlds(): array
    {
        return $this->worlds;
    }

    /**
     * @return array
     */
    public function getIgnoredWorlds(): array
    {
        return $this->ignoredWorlds;
    }
}