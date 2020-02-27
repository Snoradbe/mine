<?php


namespace App\Services\Shop\Enchants;


use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;

class Enchant implements Arrayable, Jsonable
{
    private $id;

    private $name;

    private $enchantName;

    public function __construct(int $id, string $name, string $enchantName)
    {
        $this->id = $id;
        $this->name = $name;
        $this->enchantName = $enchantName;
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
    public function getEnchantName(): string
    {
        return $this->enchantName;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'enchant_name' => $this->enchantName
        ];
    }

    /**
     * @param int $options
     * @return string
     */
    public function toJson($options = 0): string
    {
        return json_encode($this->toArray(), $options);
    }
}