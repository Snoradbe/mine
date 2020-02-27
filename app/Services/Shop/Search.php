<?php


namespace App\Services\Shop;


class Search
{
    private $server;

    /**
     * @var int|null
     */
    private $category;

    /**
     * @var string|null
     */
    private $name;

    /**
     * @var string|null
     */
    private $valute;

    /**
     * Search constructor.
     *
     * @param int|null $server
     * @param int|null $category
     * @param string|null $name
     * @param string|null $valute
     */
    public function __construct(?int $server = null, ?int $category = null, ?string $name = null, ?string $valute = null)
    {
        $this->server = $server;
        $this->category = $category;
        $this->name = $name;
        $this->valute = $valute;
    }

    /**
     * @return int|null
     */
    public function getServer(): ?int
    {
        return $this->server;
    }

    /**
     * @return int|null
     */
    public function getCategory(): ?int
    {
        return $this->category;
    }

    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @return string|null
     */
    public function getValute(): ?string
    {
        return $this->valute;
    }
}