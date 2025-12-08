<?php

namespace App\Entity;

use JulienLinard\Doctrine\Mapping\Id;
use JulienLinard\Doctrine\Mapping\Column;
use JulienLinard\Doctrine\Mapping\Entity;
use JulienLinard\Doctrine\Mapping\ManyToOne;
use JulienLinard\Doctrine\Mapping\ManyToMany;

#[Entity(table: "charts")]
class Chart
{
    #[Id] 
    #[Column(type: "integer", autoIncrement: true)]
    public ?int $id = null;

    #[ManyToOne(targetEntity: User::class, inversedBy: 'charts')]
    public ?User $user = null;

    #[ManyToMany(targetEntity: Game::class, inversedBy: 'charts', joinTable: 'charts_games', 
    joinColumns: ['chart_id'], inverseJoinColumns: ['game_id'])]
    public array $games = [];

    #[Column(type: "string", length: 100)]
    public string $status;
}