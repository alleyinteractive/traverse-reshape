<?php

/*
 * This file is part of the traverse/reshape package.
 *
 * (c) Alley <info@alley.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Alley\Tests\Fixtures;

/**
 * Implements ArrayAccess using the example internals from https://www.php.net/manual/en/class.arrayaccess.php.
 */
final class ArrayAccessible implements \ArrayAccess
{
    /**
     * Internal container.
     *
     * @var array
     */
    private array $container = [];

    /**
     * Set offset.
     *
     * @param mixed $offset Offset key.
     * @param mixed $value Value.
     */
    #[\ReturnTypeWillChange]
    public function offsetSet($offset, $value)
    {
        if (\is_null($offset)) {
            $this->container[] = $value;
        } else {
            $this->container[$offset] = $value;
        }
    }

    /**
     * Whether offset exists.
     *
     * @param mixed $offset Offset key.
     * @return bool
     */
    public function offsetExists($offset): bool
    {
        return isset($this->container[$offset]);
    }

    /**
     * Unset offset.
     *
     * @param mixed $offset Offset key.
     */
    #[\ReturnTypeWillChange]
    public function offsetUnset($offset)
    {
        unset($this->container[$offset]);
    }

    /**
     * Get offset.
     *
     * @param mixed $offset Offset key.
     * @return mixed Offset value or null.
     */
    #[\ReturnTypeWillChange]
    public function offsetGet($offset)
    {
        return $this->container[$offset] ?? null;
    }
}
