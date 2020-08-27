<?php

declare(strict_types=1);

namespace ipinfo\models;

interface IConvertibleToArray
{
    public function toArray() : array ;

}