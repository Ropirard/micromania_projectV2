<?php

namespace App\Entity;

use JulienLinard\Doctrine\Mapping\Id;
use JulienLinard\Doctrine\Mapping\Column;
use JulienLinard\Doctrine\Mapping\Entity;
use JulienLinard\Doctrine\Mapping\ManyToMany;

#[Entity(table: "genres")]
class Genre
{
    #[Id] 
    #[Column(type: "integer", autoIncrement: true)]
    public ?int $id = null;

    #[Column(type: "string", length: 100)]
    public string $name;

    #[ManyToMany(targetEntity: Game::class, mappedBy: "genres")]
    public array $games = [];
}