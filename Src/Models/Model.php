<?php

namespace App\Models;

abstract class Model
{
    abstract public function getId(): int;
    abstract public function toArray(): array;
}
