<?php


namespace App\Entity\Site;


use App\Exceptions\Exception;
use App\Services\Cabinet\CabinetSettings;
use App\Services\Permissions\Permissions;
use App\Services\Skills\Skills;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * User
 *
 * @ORM\Table(name="dle_users")
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 */
class User
{
    /**
     * @var int
     *
     * @ORM\Column(name="user_id", type="integer", nullable=false, options={"unsigned"=true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=40, nullable=false)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="uuid", type="string", length=36, nullable=false)
     */
    private $uuid;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=50, nullable=false)
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(name="password", type="string", length=255, nullable=false)
     */
    private $password;

    /**
     * @var int|null
     *
     * @ORM\Column(name="lastdate", type="integer", nullable=true)
     */
    private $lastLoginDate;

    /**
     * @var string
     *
     * @ORM\Column(name="logged_ip", type="string", length=40, nullable=false)
     */
    private $lastLoginIP;

    /**
     * @var int
     *
     * @ORM\Column(name="reg_date", type="integer", nullable=false)
     */
    private $regDate;

    /**
     * @var int
     *
     * @ORM\Column(name="cash", type="integer", nullable=false)
     */
    private $money;

    /**
     * @var int
     *
     * @ORM\Column(name="coins", type="integer", nullable=false)
     */
    private $coins;

    /**
     * @var int
     *
     * @ORM\Column(name="votes", type="integer", nullable=false)
     */
    private $votes;

    /**
     * @var int
     *
     * @ORM\Column(name="votes_all", type="integer", nullable=false)
     */
    private $votesForAllTime;

    /**
     * @var string|null
     *
     * @ORM\Column(name="lara_token", type="string", length=32, nullable=true)
     */
    private $laraToken;

    /**
     * @var int
     *
     * @ORM\Column(name="online_time", type="integer", nullable=false)
     */
    private $onlineTime = 0;

    /**
     * @var int
     *
     * @ORM\Column(name="online_time_total", type="integer", nullable=false)
     */
    private $onlineTimeTotal = 0;

    /**
     * @var int
     *
     * @ORM\Column(name="level", type="smallint", nullable=false)
     */
    private $level = 1;

    /**
     * @var int
     *
     * @ORM\Column(name="exp", type="integer", nullable=false)
     */
    private $exp = 0;

    /**
     * @var int
     *
     * @ORM\Column(name="skill_points", type="smallint", nullable=false)
     */
    private $skillPoints = 0;

    /**
     * Сколько процентов от доната принес реферал
     * @var int
     *
     * @ORM\Column(name="referal_points", type="integer", nullable=false)
     */
    private $referalPoints = 0;

    /**
     * @var string|null
     *
     * @ORM\Column(name="g2fa", type="string", length=16, nullable=true)
     */
    private $g2fa;

    /**
     * @var int|null
     *
     * @ORM\Column(name="last_server", type="integer", nullable=true)
     */
    private $lastServer;

    /**
     * @var UserHwid|null
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Site\UserHwid")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="hwid_id", referencedColumnName="id")
     * })
     */
    private $hwid;

    /**
     * @var Collection
     *
     * @ORM\OneToMany(targetEntity="App\Entity\Site\UserSkill", mappedBy="user", orphanRemoval=true, cascade={"persist", "merge", "remove"})
     */
    private $skills;

    /**
     * @var User|null
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Site\User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="referer_id", referencedColumnName="user_id")
     * })
     */
    private $referer;

    /**
     * @var Collection
     *
     * @ORM\OneToMany(targetEntity="App\Entity\Site\ReferalLog", mappedBy="user", orphanRemoval=true, cascade={"persist", "merge", "remove"})
     */
    private $referalSteps;

    /**
     * @var Collection
     *
     * @ORM\OneToMany(targetEntity="App\Entity\Site\UserGroup", mappedBy="user", orphanRemoval=true, cascade={"persist", "merge", "remove"})
     */
    private $groups;

    /**
     * @var Collection
     *
     * @ORM\OneToMany(targetEntity="App\Entity\Site\UserAdminGroup", mappedBy="user", orphanRemoval=true, cascade={"persist", "merge", "remove"})
     */
    private $adminGroups;

    /**
     * @var Permissions
     */
    private $permissions;

    /**
     * @var int
     */
    private $oldMoney;

    /**
     * @var int
     */
    private $oldCoins;

    /**
     * User constructor.
     */
    public function __construct()
    {
        $this->groups = new ArrayCollection();
        $this->adminGroups = new ArrayCollection();
        $this->permissions = new Permissions($this);
        $this->skills = new ArrayCollection();
        $this->referalSteps = new ArrayCollection();
    }

    /**
     * @ORM\PostLoad
     */
    public function postLoadHandler()
    {
        $this->permissions = new Permissions($this);
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getUuid(): string
    {
        return $this->uuid;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * @return int|null
     */
    public function getLastLoginDate(): ?int
    {
        return $this->lastLoginDate;
    }

    /**
     * @return string
     */
    public function getLastLoginIP(): string
    {
        return $this->lastLoginIP;
    }

    /**
     * @param string $lastLoginIP
     */
    public function setLastLoginIP(string $lastLoginIP): void
    {
        $this->lastLoginIP = $lastLoginIP;
    }

    /**
     * @return int
     */
    public function getRegDate(): int
    {
        return $this->regDate;
    }

    /**
     * @return int
     */
    public function getMoney(): int
    {
        return $this->money;
    }

    /**
     * @param int $money
     */
    public function setMoney(int $money): void
    {
        $this->oldMoney = $this->money;
        $this->money = $money;
    }

    /**
     * @param int $needle
     * @return bool
     */
    public function hasMoney(int $needle): bool
    {
        return $this->money >= $needle;
    }

    /**
     * @param int $amount
     * @throws Exception
     */
    public function withdrawMoney(int $amount): void
    {
        if (!$this->hasMoney($amount)) {
            throw new Exception('Недостаточно средств на балансе!');
        }

        $this->money -= $amount;
    }

    /**
     * @param int $amount
     */
    public function depositMoney(int $amount): void
    {
        $this->money += $amount;
    }

    /**
     * @return int
     */
    public function getCoins(): int
    {
        return $this->coins;
    }

    /**
     * @param int $coins
     */
    public function setCoins(int $coins): void
    {
        $this->oldCoins = $this->coins;
        $this->coins = $coins;
    }

    /**
     * @param int $needle
     * @return bool
     */
    public function hasCoins(int $needle): bool
    {
        return $this->coins >= $needle;
    }

    /**
     * @param int $amount
     * @param bool $check
     * @throws Exception
     */
    public function withdrawCoins(int $amount, bool $check = true): void
    {
        if ($check && !$this->hasCoins($amount)) {
            throw new Exception('Недостаточно поинтов!');
        }

        $this->coins -= $amount;
    }

    /**
     * @param int $amount
     */
    public function depositCoins(int $amount): void
    {
        $this->coins += $amount;
    }

    /**
     * @return int
     */
    public function getVotes(): int
    {
        return $this->votes;
    }

    /**
     * @return void
     */
    public function addVote(): void
    {
        $this->votes++;
    }

    /**
     * @return int
     */
    public function getVotesForAllTime(): int
    {
        return $this->votesForAllTime;
    }

    /**
     * @return void
     */
    public function addTotalVote(): void
    {
        $this->votesForAllTime++;
    }

    /**
     * @return string|null
     */
    public function getLaraToken(): ?string
    {
        return $this->laraToken;
    }

    /**
     * @return int
     */
    public function getOldMoney(): int
    {
        return $this->oldMoney;
    }

    /**
     * @return int
     */
    public function getOldCoins(): int
    {
        return $this->oldCoins;
    }

    /**
     * @return Collection
     */
    public function getGroups(): Collection
    {
        return $this->groups;
    }

    /**
     * @param Server $server
     * @return UserAdminGroup|null
     */
    public function getPrimaryGroup(Server $server): ?UserGroup
    {
        $group = $this->groups->filter(function (UserGroup $userGroup) use ($server) {
            return $userGroup->getGroup()->isPrimary() && $userGroup->getServer()->getId() == $server->getId();
        })->first();

        if ($group instanceof UserGroup) {
            return $group;
        }

        return null;
    }

    /**
     * @param Server|null $server
     * @param string $group
     * @return bool
     */
    public function inGroup(?Server $server, string $group): bool
    {
        return count($this->groups->filter(function (UserGroup $userGroup) use ($server, $group) {
                return
                    $userGroup->getGroup()->getName() == $group
                    && (is_null($userGroup->getServer()) || is_null($server) || $userGroup->getServer()->getId() == $server->getId())
                    && !$userGroup->isExpire();
            })) != 0;
    }

    /**
     * @return Collection
     */
    public function getAdminGroups(): Collection
    {
        return $this->adminGroups;
    }

    /**
     * @param Server $server
     * @return UserAdminGroup|null
     */
    public function getAdminGroup(Server $server): ?UserAdminGroup
    {
        $group = $this->adminGroups->filter(function (UserAdminGroup $userGroup) use ($server) {
            return is_null($userGroup->getServer()) || $userGroup->getServer()->getId() == $server->getId();
        })->first();

        if ($group instanceof UserAdminGroup) {
            return $group;
        }

        return null;
    }

    /**
     * @param Server|null $server
     * @param string $group
     * @return bool
     */
    public function inAdminGroup(?Server $server, string $group): bool
    {
        return count($this->adminGroups->filter(function (UserAdminGroup $userGroup) use ($server, $group) {
                return
                    $userGroup->getGroup()->getName() == $group
                    && (is_null($userGroup->getServer()) || is_null($server) || $userGroup->getServer()->getId() == $server->getId());
            })) != 0;
    }

    /**
     * @return bool
     */
    public function inTeam(): bool
    {
        return count($this->adminGroups) > 0;
    }

    /**
     * @return int
     */
    public function getOnlineTime(): int
    {
        return $this->onlineTime;
    }

    /**
     * @param int $amount
     */
    public function addOnlineTime(int $amount): void
    {
        $this->onlineTime += $amount;
    }

    /**
     * @return int
     */
    public function getOnlineTimeTotal(): int
    {
        return $this->onlineTimeTotal;
    }

    /**
     * @param int $amount
     */
    public function addOnlineTimeTotal(int $amount): void
    {
        $this->onlineTimeTotal += $amount;
    }

    /**
     * @return int
     */
    public function getLevel(): int
    {
        return $this->level;
    }

    /**
     * @param int $needle
     * @return bool
     */
    public function hasLevel(int $needle): bool
    {
        return $this->level >= $needle;
    }

    /**
     * @param int $level
     */
    public function setLevel(int $level): void
    {
        $this->level = $level;
    }

    /**
     * @return void
     */
    public function addLevel(): void
    {
        $this->level++;
    }

    /**
     * @return int
     */
    public function getExp(): int
    {
        return $this->exp;
    }

    /**
     * @param int $needle
     * @return bool
     */
    public function hasExp(int $needle): bool
    {
        return $this->exp >= $needle;
    }

    /**
     * @param int $exp
     */
    public function setExp(int $exp): void
    {
        $this->exp = $exp;
    }

    /**
     * @param int $amount
     */
    public function addExp(int $amount): void
    {
        $this->exp += $amount;
    }

    /**
     * @return int
     */
    public function getSkillPoints(): int
    {
        return $this->skillPoints;
    }

    /**
     * @param int $skillPoints
     */
    public function setSkillPoints(int $skillPoints): void
    {
        $this->skillPoints = $skillPoints;
    }

    /**
     * @param int $amount
     */
    public function addSkillPoints(int $amount): void
    {
        $this->skillPoints += $amount;
    }

    /**
     * @param int $needle
     * @return bool
     */
    public function hasSkillPoints(int $needle): bool
    {
        return $this->skillPoints >= $needle;
    }

    /**
     * @param int $amount
     * @param bool $check
     * @throws Exception
     */
    public function withdrawSkillPoints(int $amount, bool $check = true): void
    {
        if ($check && !$this->hasSkillPoints($amount)) {
            throw new Exception('Не хватает поинтов!');
        }

        $this->skillPoints -= $amount;
    }

    /**
     * @return Collection
     */
    public function getSkills(): Collection
    {
        return $this->skills;
    }

    /**
     * @param Server|null $server
     * @param int $skillId
     * @return UserSkill|null
     */
    public function getSkill(?Server $server, int $skillId): ?UserSkill
    {
        $userSkill = $this->skills->filter(function (UserSkill $userSkill) use ($server, $skillId) {
            return (is_null($userSkill->getSkill()->getServer()) || $userSkill->getSkill()->getServer() === $server)
                &&
                $userSkill->getSkill()->getId() == $skillId;
        })->first();

        if ($userSkill instanceof UserSkill) {
            return $userSkill;
        }

        return null;
    }

    /**
     * @param Server|null $server
     * @param int $skillId
     * @return bool
     */
    public function hasSkill(?Server $server, int $skillId): bool
    {
        $userSkill = $this->getSkill($server, $skillId);

        return !is_null($userSkill);
    }

    /**
     * @param Server|null $server
     * @param int $skillId
     * @return int
     */
    public function getSkillLevel(?Server $server, int $skillId): int
    {
        $userSkill = $this->getSkill($server, $skillId);

        if (!is_null($userSkill)) {
            return $userSkill->getLevel();
        }

        return 0;
    }

    /**
     * @return User|null
     */
    public function getReferer(): ?User
    {
        return $this->referer;
    }

    /**
     * @return bool
     */
    public function hasReferer(): bool
    {
        return !is_null($this->referer);
    }

    /**
     * @return Collection
     */
    public function getReferalSteps(): Collection
    {
        return $this->referalSteps;
    }

    /**
     * @return int
     */
    public function getReferalPoints(): int
    {
        return $this->referalPoints;
    }

    /**
     * @param int $amount
     */
    public function depositReferalPoints(int $amount): void
    {
        $this->referalPoints += $amount;
    }

    /**
     * @return string|null
     */
    public function getG2fa(): ?string
    {
        return $this->g2fa;
    }

    /**
     * @return bool
     */
    public function hasG2fa(): bool
    {
        return !is_null($this->g2fa);
    }

    /**
     * @param string|null $g2fa
     */
    public function setG2fa(?string $g2fa): void
    {
        $this->g2fa = $g2fa;
    }

    /**
     * @return UserHwid|null
     */
    public function getHwid(): ?UserHwid
    {
        return $this->hwid;
    }

    /**
     * @return bool
     */
    public function hasHwid(): bool
    {
        return !is_null($this->hwid);
    }

    /**
     * @param bool $value
     * @throws Exception
     */
    public function hwidBan(bool $value): void
    {
        if (!$this->hasHwid()) {
            throw new Exception('У этого игрока HWID не записан. Возможно он еще не авторизовывался');
        }

        $this->hwid->setBanned($value);
    }

    /**
     * @return int|null
     */
    public function getLastServer(): ?int
    {
        return $this->lastServer;
    }

    /**
     * @param int|null $lastServer
     */
    public function setLastServer(?int $lastServer): void
    {
        $this->lastServer = $lastServer;
    }

    /**
     * @return Permissions
     */
    public function permissions(): Permissions
    {
        return $this->permissions;
    }

    /**
     * @ORM\PostLoad
     */
    public function postLoad(): void
    {
        $this->oldMoney = $this->money;
        $this->oldCoins = $this->coins;
    }
	
    /**
     * @return array
     */
	public function toSimpleArray(): array
	{
		return [
			'id' => $this->id,
			'name' => $this->name,
			'uuid' => $this->uuid
		];
	}

    /**
     * @param bool $secret
     * @return array
     */
    public function toArray(bool $secret = false): array
    {
        $groups = [];
        $adminGroups = [
            'all' => []
        ];

        /* @var UserGroup $group */
        foreach ($this->groups as $group)
        {
            $groups[$group->getServer()->getId()][] = [
                'id' => $group->getId(),
                'group' => $group->getGroup()->toArray(),
                'start' => $group->getCreatedAt(),
                'expire' => $group->getExpireAt()
            ];
        }

        /* @var UserAdminGroup $adminGroup */
        foreach ($this->adminGroups as $adminGroup)
        {
            $serverId = is_null($adminGroup->getServer()) ? 'all' : $adminGroup->getServer()->getId();
            $adminGroups[$serverId][] = [
                'id' => $adminGroup->getId(),
                'group' => $adminGroup->getGroup()->toArray(),
                'start' => $adminGroup->getCreatedAt()->getTimestamp()
            ];
        }

        $data = [
            'id' => $this->id,
            'name' => $this->name,
            'uuid' => $this->uuid,
            'last_login' => $this->lastLoginDate,
            'reg_date' => $this->regDate,
            'level' => $this->level,
            'exp' => $this->exp,
            'groups' => $groups,
            'admin_groups' => $adminGroups,
            'permissions' => [
                'common' => CabinetSettings::getDefaultPermissions()
            ],
        ];
        
        if ($secret) {
            $skills = [
                'all' => []
            ];
            /** @var UserSkill $skill */
            foreach ($this->skills as $skill)
            {
                if (is_null($skill->getSkill()->getServer())) {
                    $skills['all'][] = $skill->toArray();
                } else {
                    if (!isset($skills[$skill->getSkill()->getServer()->getId()])) {
                        $skills[$skill->getSkill()->getServer()->getId()] = [];
                    }
                    $skills[$skill->getSkill()->getServer()->getId()][] = $skill->toArray();
                }
            }

            $data['referal_ponts'] = $this->referalPoints;
            $data['skill_points'] = $this->skillPoints;
            $data['skills'] = $skills;
            $data['email'] = $this->email;
            $data['last_login_ip'] = $this->lastLoginIP;
            $data['money'] = $this->money;
            $data['coins'] = $this->coins;
            $data['votes'] = $this->votes;
            $data['votes_all'] = $this->votesForAllTime;
        }

        return $data;
    }
}