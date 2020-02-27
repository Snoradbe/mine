<?php


namespace App\Services\Voting\Tops;


class Pool
{
    /**
     * @var Top[]
     */
    private $tops;

    /**
     * Pool constructor.
     *
     * @param Top[] $tops
     */
    public function __construct(array $tops)
    {
        $this->tops = $tops;
    }

    /**
     * @param string $name
     * @return Top|null
     */
    public function retrieveByName(string $name): ?Top
    {
        foreach ($this->tops as $top)
        {
            if ($top->getName() == $name) {
                return $top;
            }
        }

        return null;
    }

    /**
     * @return Top[]
     */
    public function all(): array
    {
        return $this->tops;
    }
}