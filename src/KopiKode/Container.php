<?php

namespace KopiKode;

use \Interop\Container\ContainerInterface;
use \KopiKode\Exception\InvalidArgumentException;
use \KopiKode\Exception\InvalidIdentifierException;

class Container implements ContainerInterface, \ArrayAccess, \IteratorAggregate, \Countable
{
    /**
     * Array container
     *
     * @type array
     */
    protected $properties;

    public function __construct(array $defaults = array())
    {
        foreach ($defaults as $key => $value) {
            $this[$key] = $value;
        }
    }

    public function set($offset, $value)
    {
        $offset = self::canonicalize($offset);
        if (strpos($offset, '.')) {
            $arrOffset = explode('.', $offset);
            $arrData = array();
            $data = $this->properties;
            foreach ($arrOffset as $key) {
                if (isset($data[$key])) {
                    $data = $data[$key];
                    $arrData[$key] = $data;
                } else {
                    $arrData[$key] = array();
                }
            }

            $setData = null;
            $revOffset = array_reverse($arrOffset);

            foreach ($revOffset as $key) {
                if (isset($arrData[$key])) {
                    if ($setData === null) {
                        $setData = array($key => $value);
                    } else {
                        if (is_array($arrData[$key])) {
                            $setData = array($key => array_replace($arrData[$key], $setData));
                        } else {
                            $setData = array($key => $value);
                        }
                    }
                } else {
                    $setData = ($setData === null) ? array($key => $value) : array($key => $setData);
                }
            }

            $this->properties = array_replace($this->properties, $setData);
        } else {
            $this->properties[$offset] = $value;
        }
    }

    public function get($offset)
    {
        $offset = self::canonicalize($offset);
        if (strpos($offset, '.')) {
            $arrOffset = explode('.', $offset);

            $data = $this->properties;
            $retValue = null;

            foreach ($arrOffset as $key) {
                if (isset($data[$key])) {
                    $data = $data[$key];
                    $retValue = $data;
                } else {
                    $retValue = null;
                    break;
                }
            }

            if ($retValue ===  null) {
                throw new InvalidIdentifierException("Invalid properties identifier ( ". $offset . " )");
            }

            if (method_exists($retValue, '__invoke')) {
                return $retvalue($this);
            } else {
                return $retValue;
            }
        } else {
            if (!$this->has($offset)) {
                throw new InvalidIdentifierException("Invalid properties identifier ( ". $offset . " )");
            }

            if (method_exists($this->properties[$offset], '__invoke')) {
                return $this->properties[$offset]($this);
            } else {
                return $this->properties[$offset];
            }
        }
    }

    public function has($offset)
    {
        if (strpos($offset, '.')) {
            $arrOffset = explode('.', $offset);

            $data = $this->properties;
            $found = false;

            foreach ($arrOffset as $key) {
                if (array_key_exists($key, $data)) {
                    $data = $data[$key];
                    $found = true;
                } else {
                    $found = false;
                }
            }
            return $found;
        } else {
            return array_key_exists(self::canonicalize($offset), $this->properties);
        }
    }

    public function delete($offset)
    {
        $offset = self::canonicalize($offset);
        if (strpos($offset, '.')) {
            $arrOffset = explode('.', $offset);

            $data = $this->properties;

            $idx = 0;
            foreach ($arrOffset as $key) {
                if (isset($data[$key])) {
                    $data = $data[$key];
                    if ($idx == (count($arrOffset) - 2)) {
                        $lastKey = array_pop($arrOffset);
                        unset($data[$lastKey]);
                        $this->set(implode('.', $arrOffset), $data);
                    }

                    $idx++;
                } else {
                    break;
                }
            }
        } else {
            unset($this->properties[self::canonicalize($offset)]);
        }
    }

    public static function canonicalize($key)
    {
        if (!preg_match('/[a-z0-9\.\_]+/', $key)) {
            throw new InvalidIdentifierException("Invalid properties identifier ( " . $key . " )");
        }

        return strtolower($key);
    }

    public function offsetGet($offset)
    {
        return $this->get($offset);
    }

    public function offsetSet($offset, $value)
    {
        $this->set($offset, $value);
    }

    public function offsetExists($offset)
    {
        return $this->has($offset);
    }

    public function offsetUnset($offset)
    {
        $this->delete($offset);
    }

    public function count()
    {
        return count($this->properties);
    }

    public function getIterator()
    {
        return new ArrayIterator($this->properties);
    }
}
