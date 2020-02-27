<?php


namespace App\Entity\Site;


use Doctrine\ORM\Mapping as ORM;
use Illuminate\Support\Str;

/**
 * Group
 *
 * @ORM\Table(name="pr_groups", uniqueConstraints={@ORM\UniqueConstraint(name="pr_groups_name_unique", columns={"name"})})
 * @ORM\Entity
 */
class Group
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false, options={"unsigned"=true})
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=24, nullable=false)
     */
    private $name;

    /**
     * @var int
     *
     * @ORM\Column(name="weight", type="integer", nullable=false, options={"comment"="Вес группы. Чем выше, тем лучше"})
     */
    private $weight = 0;

    /**
     * @var bool
     *
     * @ORM\Column(name="is_primary", type="boolean", nullable=false, options={"comment"="Является ли группа основной (vip, premium...)?"})
     */
    private $isPrimary;

    /**
     * @var bool
     *
     * @ORM\Column(name="is_admin", type="boolean", nullable=false, options={"comment"="Является ли группа админской?"})
     */
    private $isAdmin;

    /**
     * @var string|null
     *
     * @ORM\Column(name="permission_name", type="string", length=255, nullable=true, options={"comment"="Название пермишена для доп. групп (god, fly...)"})
     */
    private $permissionName;

    /**
     * @var string|null
     *
     * @ORM\Column(name="permissions", type="text", nullable=true, options={"comment"="Права на сайте, через запятую"})
     */
    private $permissions = null;

    /**
     * @var array
     */
    private $permissionsList = null;

    /**
     * @var null|int
     *
     * @ORM\Column(name="forum_id", type="smallint", nullable=true, options={"comment"="Группа на форуме"})
     */
    private $forumId = null;

    /**
     * @var Group|null
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Site\Group")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="parent_id", referencedColumnName="id")
     * })
     */
    private $parent;

    /**
     * Group constructor.
     *
     * @param string $name
     * @param int $weight
     * @param bool $isPrimary
     * @param bool $isAdmin
     * @param string|null $permissionName
     * @param int|null $forumId
     */
    public function __construct(
        string $name,
        int $weight,
        bool $isPrimary,
        bool $isAdmin,
        ?string $permissionName = null,
        ?int $forumId = null)
    {
        $this->name = $name;
        $this->weight = $weight;
        $this->isPrimary = $isPrimary;
        $this->isAdmin = $isAdmin;
        $this->permissionName = $permissionName;
        $this->forumId = $forumId;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return Group|null
     */
    public function getParent(): ?Group
    {
        return $this->parent;
    }

    /**
     * @param Group|null $parent
     */
    public function setParent(?Group $parent): void
    {
        $this->parent = $parent;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return int
     */
    public function getWeight(): int
    {
        return $this->weight;
    }

    /**
     * @param int $weight
     */
    public function setWeight(int $weight): void
    {
        $this->weight = $weight;
    }

    /**
     * @return bool
     */
    public function isPrimary(): bool
    {
        return $this->isPrimary;
    }

    /**
     * @param bool $isPrimary
     */
    public function setIsPrimary(bool $isPrimary): void
    {
        $this->isPrimary = $isPrimary;
    }

    /**
     * @return bool
     */
    public function isAdmin(): bool
    {
        return $this->isAdmin;
    }

    /**
     * @param bool $isAdmin
     */
    public function setIsAdmin(bool $isAdmin): void
    {
        $this->isAdmin = $isAdmin;
    }

    /**
     * @return int|null
     */
    public function getForumId(): ?int
    {
        return $this->forumId;
    }

    /**
     * @param int|null $forumId
     */
    public function setForumId(?int $forumId): void
    {
        $this->forumId = $forumId;
    }

    /**
     * @return null|string
     */
    public function getPermissionName(): ?string
    {
        return $this->permissionName;
    }

    /**
     * @param string|null $permissionName
     */
    public function setPermissionName(?string $permissionName): void
    {
        $this->permissionName = $permissionName;
    }

    /**
     * Используется ТОЛЬКО для default группы!
     *
     * @param array $permissions
     */
    public function setPermissions(array $permissions): void
    {
        $this->permissions = implode(',', $permissions);
        $this->permissionsList = $permissions;
    }

    /**
     * @return array
     */
    public function getPermissions(): array
    {
        if (!is_array($this->permissionsList)) {
            if (empty($this->permissions)) {
                $this->permissionsList = [];
            } else {
                $this->permissionsList = explode(',', $this->permissions);
            }
        }

        return $this->permissionsList;
    }

    /**
     * @param string $permission
     * @return bool
     */
    public function hasPermission(string $permission): bool
    {
        if (
            in_array($permission, $this->getPermissions()) //Это право
            ||
            $this->fullPermission($permission)
        ) {
            return true;
        }

        $expl = explode('.', $permission, -1);
        if (!isset($expl[0])) {
            return false;
        }

        if (in_array($expl[0] . '.*', $this->getPermissions())) {
            return true;
        }

        $perm = $expl[0];
        unset($expl[0]);

        foreach ($expl as $segment)
        {
            $perm .= '.' . $segment;
            if (in_array($perm . '.*', $this->getPermissions())) {
                return true;
            }
        }

        if (!is_null($this->parent)) {
            return $this->parent->hasPermission($permission);
        }

        return false;
    }

    private function fullPermission(string $permission): bool
    {
        return
            in_array('*', $this->getPermissions()) //Все права
            ||
            in_array($permission . '.*', $this->getPermissions()) //Все права этого пермишена
            ;
    }

    private function exactMatchPermission(string $permission): bool
    {
        return in_array($permission, $this->getPermissions());
    }

    public function containsPermissionPrefix(string $prefix): bool
    {
        if ($this->hasPermission($prefix)) {
            return true;
        }

        foreach ($this->getPermissions() as $permission)
        {
            if (Str::startsWith($permission, $prefix)) {
                return true;
            }
        }
		
		if (!is_null($this->parent)) {
			return $this->parent->containsPermissionPrefix($prefix);
		}

        return false;
    }

    /**
     * @param string $permission
     */
    public function addPermission(string $permission): void
    {
        $permission = $this->filterPermission($permission);
        if ($this->hasPermission($permission)) {
            return;
        }

        $this->permissionsList[] = $permission;
        $this->updatePermissions();
    }

    /**
     * @param string $permission
     */
    public function removePermission(string $permission): void
    {
        $permission = $this->filterPermission($permission);
        if (!$this->exactMatchPermission($permission)) {
            return;
        }

        unset($this->permissionsList[array_search($permission, $this->permissionsList)]);
        $this->updatePermissions();
    }

    /**
     * @param string $permission
     * @return string
     */
    private function filterPermission(string $permission): string
    {
        return strtolower($permission);
    }

    /**
     * @return void
     */
    private function updatePermissions(): void
    {
        $this->permissions = implode(',', $this->permissionsList);
    }

    /**
     * @param bool $secret
     * @return array
     */
    public function toArray(bool $secret = true): array
    {
        $data = [
            'id' => $this->id,
            'name' => $this->name,
            'permissions' => $this->getPermissions(),
            'parent' => is_null($this->parent) ? null : $this->parent->toArray()
        ];

        if ($secret) {
            $data = array_merge($data, [
                'weight' => $this->weight,
                'isPrimary' => $this->isPrimary,
                'isAdmin' => $this->isAdmin,
            ]);
        }

        return $data;
    }
}