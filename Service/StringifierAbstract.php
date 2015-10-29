<?php

namespace Baskin\HistoryBundle\Service;

/**
 * Class StringifierAbstract
 * @package Baskin\HistoryBundle\Service
 */
abstract class StringifierAbstract implements StringifierInterface
{
    /**
     * @param mixed $value
     * @return string
     */
    public function getString($value)
    {
        if (is_string($value)) {
            return $this->fromString($value);
        }
        if (is_int($value)) {
            return $this->fromInt($value);
        }
        if (is_double($value)) {
            return $this->fromDouble($value);
        }
        if (is_bool($value)) {
            return $this->fromBoolean($value);
        }
        if ($value instanceof \DateTime) {
            return $this->fromDateTime($value);
        }
        if ($value instanceof \Traversable || is_array($value)) {
            return $this->fromIterable($value);
        }
        if (is_object($value)) {
            return $this->fromIterable($value);
        }
        if (empty($value)) {
            return $this->fromEmpty();
        }

        return $this->fromOther();
    }

    /**
     * @param string $value
     * @return string
     */
    protected function fromString($value)
    {
        return $value;
    }

    /**
     * @param int $value
     * @return string
     */
    protected function fromInt($value)
    {
        return strval($value);
    }

    /**
     * @param double $value
     * @return string
     */
    protected function fromDouble($value)
    {
        return number_format($value, 2, ',', '.');
    }

    /**
     * @param bool $value
     * @return string
     */
    protected function fromBoolean($value)
    {
        return $value ? "TRUE" : "FALSE";
    }

    /**
     * @param \DateTime $value
     * @return string
     */
    protected function fromDateTime(\DateTime $value)
    {
        return $value->format('c');
    }

    /**
     * @param \Traversable|Array $value
     * @return string
     */
    protected function fromIterable($value)
    {
        $array = array();
        foreach ($value as $item) {
            $array[] = $this->getString($item);
        }

        if (empty($array)) {
            return $this->fromEmpty();
        }

        return implode(', ', $array);
    }

    /**
     * @return string
     */
    protected function fromEmpty()
    {
        return '-';
    }

    /**
     * @return string
     */
    protected function fromOther()
    {
        return '-';
    }

    /**
     * @param Object $value
     * @return string
     */
    protected function fromObject($value)
    {
        if (method_exists('getHistoryValue', $value)) {
            return (string)call_user_func(array($value, 'getHistoryValue'));
        }

        if (method_exists('__toString', $value)) {
            return (string)$value;
        }

        return '[OBJECT]';
    }
}
