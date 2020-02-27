<?php


namespace App\Entity\Game;


use Doctrine\ORM\Mapping as ORM;

/**
 * ScreenShoter
 *
 * @ORM\Table(name="screenshoter_history")
 * @ORM\Entity
 */
class ScreenShoter
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
     * @ORM\Column(name="admin", type="string", length=24, nullable=false)
     */
    private $admin;

    /**
     * @var string
     *
     * @ORM\Column(name="username", type="string", length=24, nullable=false)
     */
    private $username;

    /**
     * @var string
     *
     * @ORM\Column(name="response", type="string", length=255, nullable=false)
     */
    private $response;

    /**
     * @var \DateTimeImmutable
     *
     * @ORM\Column(name="date", type="datetime_immutable", nullable=false)
     */
    private $date;

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
    public function getAdmin(): string
    {
        return $this->admin;
    }

    /**
     * @return string
     */
    public function getUsername(): string
    {
        return $this->username;
    }

    /**
     * @return string
     */
    public function getResponse(): string
    {
        return $this->response;
    }

    /**
     * @return \DateTimeImmutable
     */
    public function getDate(): \DateTimeImmutable
    {
        return $this->date;
    }

    /**
     * @param string $size s|m|l
     * @return string
     */
    public function getPreview(string $size = 'm'): string
    {
        $prefix = 'https://i.imgur.com/';

        $expl = explode('.', str_replace($prefix, '', $this->response));
        if (count($expl) == 2) {
            return $prefix . $expl[0] . $size . '.' . $expl[1];
        }

        return $this->response;
    }
}