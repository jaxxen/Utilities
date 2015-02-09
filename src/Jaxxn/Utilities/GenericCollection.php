<?php

namespace Jaxxn\Utilities;

use ArrayAccess;
use ArrayIterator;
use Countable;
use IteratorAggregate;
use Jaxxn\Utilities\Interfaces\IArrayable;
use Jaxxn\Utilities\Interfaces\IJsonable;
use Jaxxn\Utilities\Interfaces\IMakeable;
use JsonSerializable;

class GenericCollection implements  Countable, ArrayAccess, IteratorAggregate, IJsonable, IArrayable, IMakeable, JsonSerializable
{
    /**
     * Collection items
     *
     * @var
     */
    private $items;

    /**
     * Converts array and children to Generic collections
     *
     * @param array $items
     */
    public function __construct(array $items)
    {
        $this->addItems($items);
    }

    /**
     * Adds contents of an array to collection. Converts to collection.
     *
     * @param $items
     */
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

    /**
     * Get an item from collection
     *
     * @param $key
     * @return null
     */
    public function get($key)
    {
        return ($this->has($key)) ? $this->items[$key] : null;
    }

    /**
     * @param $key
     * @param $value
     */
    public function add($key, $value)
    {
        $key = $this->hasNumericKey($key);
        if(is_array($value))
        {
            $value = GenericCollection::make($value);
        }

        $this->items[$key] = $value;
    }

    /**
     * Check if value exists in collection
     *
     * @param $value
     * @return mixed
     */
    public function contains($value)
    {
        return array_search($value, $this->items);
    }

    /**
     * Removes an item in collection
     *
     * @param $key
     */
    public function remove($key)
    {
        unset($this->items[$key]);
    }

    /**
     * Serializes the collection
     *
     * @return string
     */
    public function serialize()
    {
        return serialize($this->items);
    }

    /**
     * Check if key exists in collection
     *
     * @param $key
     * @return bool
     */
    public function has($key)
    {
        if(is_null($this->items))
        {
            return false;
        }

        return array_key_exists($key, $this->items);
    }

    /**
     * Helper function for checking if numeric key exists in collection.
     *
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

    /**
     * Get all keys in collection
     *
     * @return array
     */
    public function getKeys()
    {
        return array_keys($this->items);
    }

    /**
     * Get all values in collection
     *
     * @return array
     */
    public function getValues()
    {
        return array_values($this->items);
    }

    /**
     * Get first item in collection
     *
     * @return mixed
     */
    public function first()
    {
        return array_values($this->items)[0];
    }

    /**
     * Get last item in collection
     *
     * @return mixed
     */
    public function last()
    {
        return end($this->items);
    }

    /**
     * Get count of collection
     *
     * @return int
     */
    public function count()
    {
        return count($this->items);
    }

    #region Interface arrayacces
    /**
     * @param mixed $offset
     * @return bool
     */
    public function offsetExists($offset)
    {
        return $this->has($offset);
    }


    /**
     * @param mixed $offset
     * @return null
     */
    public function offsetGet($offset)
    {
        return $this->get($offset);
    }

    /**
     * @param mixed $offset
     * @param mixed $value
     */
    public function offsetSet($offset, $value)
    {
        $this->add($offset, $value);
    }

    /**
     * @param mixed $offset
     */
    public function offsetUnset($offset)
    {
        $this->remove($offset);
    }
    #endregion

    /**
     * Make all keys lowercase
     */
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

    /**
     * Gets the array iterator
     *
     * @return ArrayIterator
     */
    public function getIterator()
    {
        return new ArrayIterator($this->items);
    }

    /**
     * convert collection to json
     *
     * @return string
     */
    function toJson()
    {
        return json_encode($this->items, true);
    }

    /**
     * convert collection to array
     *
     * @return mixed
     */
    function toArray()
    {
        return json_decode(json_encode($this->items), true);;
    }

    /**
     * Statically make an collection
     *
     * @param array $items
     * @return static
     */
    static function make(array $items)
    {
        return new static($items);
    }

    /**
     * Needed fro json_encode
     *
     * @return mixed
     */
    function jsonSerialize()
    {
        return $this->items;
    }

    /**
     * Returns collection serialized.
     *
     * @return string
     */
    function __toString()
    {
        return $this->serialize();
    }

}