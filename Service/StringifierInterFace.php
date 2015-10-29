<?php

namespace Baskin\HistoryBundle\Service;

/**
 * Interface StringifierInterface
 * @package Baskin\HistoryBundle\Service
 */
interface StringifierInterface
{
    /**
     * @param mixed $value
     * @return string
     */
    public function getString($value);

}
