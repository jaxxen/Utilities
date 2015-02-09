<?php

namespace Jaxxn\Support;

use ArrayAccess;
use ArrayIterator;
use Countable;
use IteratorAggregate;
use Jaxxn\Support\Interfaces\IArrayable;
use Jaxxn\Support\Interfaces\IJsonable;
use Jaxxn\Support\Interfaces\IMakeable;
use JsonSerializable;

class GenericCollection implements  Countable, ArrayAccess, IteratorAggregate, IJsonable, IArrayable, IMakeable, JsonSerializable
{
    private $items;

    public function __construct(array $items)
    {
        $this->addItems($items);
    }

    public function addItems($items)
    {
        foreach($items as $key => $item)
        {
            if(is_array($item)){
                $item = new Static($item);
            }

            $this->add($key, $item);
        }
    }

    public function get($key)
    {
        return ($this->has($key)) ? $this->items[$key] : null;
    }

    public function add($key, $value)
    {
        $key = $this->hasNumericKey($key);
        if(is_array($value))
        {
            $value = GenericCollection::make($value);
        }

        $this->items[$key] = $value;
    }

    public function contains($value)
    {
        return array_search($value, $this->items);
    }

    public function remove($key)
    {
        unset($this->items[$key]);
    }

    public function serialize()
    {
        return serialize($this->items);
    }

    public function has($key)
    {
        if(is_null($this->items))
        {
            return false;
        }

        return array_key_exists($key, $this->items);
    }

    /**
     * @param $key
     * @return int|string
     */
    private function hasNumericKey($key)
    {
        if (is_numeric($key)) {
            if ($this->has($key)) {
                while ($this->has($key)) {
                    $key++;
                }

                return $key;
            }

            return $key;
        }

        return $key;
    }

    public function getKeys()
    {
        return array_keys($this->items);
    }

    public function getValues()
    {
        return array_values($this->items);
    }

    public function first()
    {
        return array_values($this->items)[0];
    }

    public function last()
    {
        return end($this->items);
    }

    public function count()
    {
        return count($this->items);
    }

    #region Interface arrayacces
    public function offsetExists($offset)
    {
        return $this->has($offset);
    }


    public function offsetGet($offset)
    {
        return $this->get($offset);
    }

    public function offsetSet($offset, $value)
    {
        $this->add($offset, $value);
    }

    public function offsetUnset($offset)
    {
        $this->remove($offset);
    }
    #endregion

    public function homogenizeKeys()
    {
        if(empty($this->items)) {
            return;
        }

        foreach($this->items as $key => $item)
        {
            $this->remove($key);
            $this->items[strtolower($key)] = $item;
        }
    }
    public function getIterator()
    {
        return new ArrayIterator($this->items);
    }

    function toJson()
    {
        return json_encode($this->items, true);
    }

    function toArray()
    {
        return json_decode(json_encode($this->items), true);;
    }

    static function make(array $items)
    {
        return new static($items);
    }

    function jsonSerialize()
    {
        return $this->items;
    }

    /*
     * Decisions ... decisions.
     *
    function __get($name)
    {
        return $this->get($name);
    }

    function __set($name, $value)
    {
        $this->add($name, $value);
    }
    */

    function __toString()
    {
        return $this->serialize();
    }


}