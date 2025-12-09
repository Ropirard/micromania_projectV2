<?php

namespace App\Entity;

use JulienLinard\Doctrine\Mapping\Id;
use JulienLinard\Doctrine\Mapping\Column;
use JulienLinard\Doctrine\Mapping\Entity;
use JulienLinard\Doctrine\Mapping\ManyToOne;
use JulienLinard\Doctrine\Mapping\ManyToMany;
use JulienLinard\Doctrine\Mapping\OneToMany;

#[Entity(table: "games")]
class Game
{
    #[Id] 
    #[Column(type: "integer", autoIncrement: true)]
    public ?int $id = null;

    #[Column(type: "string", length: 100)]
    public string $title;

    #[Column(type: "text")]
    public string $description;

    #[Column(type: "decimal")]
    public float $price;

    #[Column(type: "integer")]
    public int $stock;

    #[ManyToMany(targetEntity: Chart::class, mappedBy: 'games')]
    public array $charts = [];

    #[ManyToMany(targetEntity: Genre::class, joinTable: 'games_genres')]
    public array $genres = [];

    #[ManyToMany(targetEntity: Plateform::class, joinTable: 'games_plateforms')]
    public array $plateforms = [];

    #[OneToMany(targetEntity: Media::class, mappedBy: 'game')]
    public array $media = [];
}