<?php

namespace App\Model;

class CurrencyModel
{
    private string $name;

    private string $slug;

    // getters
    public function getName(): string
    {
        return $this->name;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    // setters
    public function setName($name): string
    {
        return $this->name = $name;
    }

    public function setSlug($slug): string
    {
        return $this->slug = $slug;
    }
}
