<?php


namespace App\Services\Settings;


use App\Entity\Site\Setting;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;

class DefaultSettings implements Settings
{
    /**
     * @var Driver
     */
    private $driver;

    /**
     * @var Store
     */
    private $store;

    /**
     * @var Setting[]
     */
    private $originalData;

    /**
     * DefaultSettings constructor.
     * @param Driver $driver
     */
    public function __construct(Driver $driver)
    {
        $this->driver = $driver;
        $this->originalData = $driver->getAll();

        $new = [];
        foreach ($this->originalData as $originalDatum)
        {
            $new[] = clone $originalDatum;
        }
        $this->store = new Store($new);
    }

    public function get(string $key, $default = null)
    {
        if ($this->exists($key)) {
            return $this->store->get($key);
        }

        return $default;
    }

    public function set(string $key, $value): void
    {
        if ($value instanceof \JsonSerializable) {
            $value = json_encode($value);
        }
        if ($value instanceof Jsonable) {
            $value = $value->toJson();
        }
        if ($value instanceof Arrayable) {
            $value = $value->toArray();
        }
        if ($value instanceof \Serializable || is_object($value)) {
            $value = serialize($value);
        }
        if (is_array($value)) {
            $value = json_encode($value);
        }
        if (is_bool($value)) {
            $value = (int)$value;
        }

        $this->store->set($key, $value);
    }

    public function forget(string $key): bool
    {
        return $this->store->remove($key);
    }

    public function setArray(array $data): void
    {
        $data = array_dot($data);
        foreach ($data as $key => $value)
        {
            $this->set($key, $value);
        }
    }

    public function exists(string $key): bool
    {
        return $this->store->exists($key);
    }

    public function flush(): void
    {
        $this->store->flush();
    }

    public function save(): void
    {
        $this->driver->write($this->originalData, $this->store->all());
        $this->originalData = $this->store->all();
    }
}