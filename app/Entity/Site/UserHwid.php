<?php


namespace App\Entity\Site;


use Doctrine\ORM\Mapping as ORM;

/**
 * UserHwid
 *
 * @ORM\Table(name="pr_user_hwids")
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 */
class UserHwid
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
     * @var boolean
     *
     * @ORM\Column(name="is_banned", type="boolean", nullable=false)
     */
    private $banned;

    /**
     * @var string
     *
     * @ORM\Column(name="total_memory", type="text", nullable=false)
     */
    private $totalMemory;

    /**
     * @var string
     *
     * @ORM\Column(name="serial_number", type="text", nullable=false)
     */
    private $serialNumber;

    /**
     * @var string
     *
     * @ORM\Column(name="hw_disk_serial", type="text", nullable=false)
     */
    private $HWDiskSerial;

    /**
     * @var string
     *
     * @ORM\Column(name="processor_id", type="text", nullable=false)
     */
    private $processorID;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return bool
     */
    public function isBanned(): bool
    {
        return $this->banned;
    }

    /**
     * @param bool $banned
     */
    public function setBanned(bool $banned): void
    {
        $this->banned = $banned;
    }

    /**
     * @return string
     */
    public function getTotalMemory(): string
    {
        return $this->totalMemory;
    }

    /**
     * @return string
     */
    public function getSerialNumber(): string
    {
        return $this->serialNumber;
    }

    /**
     * @return string
     */
    public function getHWDiskSerial(): string
    {
        return $this->HWDiskSerial;
    }

    /**
     * @return string
     */
    public function getProcessorID(): string
    {
        return $this->processorID;
    }
}