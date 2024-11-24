<?php

namespace App\DataFixtures;

use App\Interface\IAudit;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;

abstract class BaseFixtures extends Fixture
{
    private array $names = ["João Pedro", "Maria Luiza", "Antonio Carlos", "Beatriz Silva", "Carlos Eduardo",
        "Felipe Alves", "Camila Rodrigues", "Matheus Ferreira", "Larissa Martins", "Vinicius Lima", "Amanda Pereira",
        "Pedro Lucas", "Juliana Araújo", "Rafael Cardoso", "Gabriela Silva", "Luiz Gustavo", "Isabela Santos",
        "Matheus Oliveira", "Camila Alves", "Vinicius Costa", "Amanda Rodrigues", "Thiago Ferreira", "Juliana Martins",
        "Anderson Lima", "Caroline Souza", "Lucas Ribeiro", "Beatriz Gonçalves", "Ricardo Dias"];

    private array $usernames = ["skywalker_21", "tech_guru85", "ocean_dreamer", "wild_adventurer", "urban_explorer", "code_master007",
        "galaxy_traveler", "mountain_hiker89", "sunset_chaser", "starry_knight", "jungle_scout", "wanderlust_123",
        "digital_nomad87", "coffee_lover99", "pixel_artist", "retro_gamer77", "mystic_rider", "cyber_phantom",
        "urban_rebel", "zen_meditator", "cosmic_wave", "speed_runner", "quiet_mind123", "creative_vibes",
        "alpha_coder", "wildflower_soul", "artistic_mind", "sunny_horizons", "deep_thinker", "tech_savant42",
        "wanderer_88", "night_owl90", "desert_nomad", "forest_whisperer", "lone_wolf75", "silver_surfer_22"];

    protected array $places = [
        "Fernando de Noronha por 7 dias", "3 semanas explorando o Pantanal", "Chapada Diamantina em 5 dias",
        "10 dias na cidade de Salvador", "Porto de Galinhas em 4 dias", "2 semanas na Amazônia",
        "No Jalapão por 8 dias", "6 dias conhecendo Foz do Iguaçu", "Fortaleza em 12 dias de praias",
        "Em Brasília por 3 dias", "Florianópolis por 9 dias", "Chapada dos Veadeiros em 5 dias",
        "1 semana vivenciando São Paulo", "Rio de Janeiro em 11 dias de visitas", "4 dias pela Serra Gaúcha",
        "Ouro Preto por 6 dias", "Lençóis Maranhenses explorados em 15 dias", "10 dias em Trancoso"];

    protected $companyName = ["Turismo Aventura BR", "Excursões Tropicais", "Viagem Brasil Tours",
        "Descubra o Brasil Turismo", "Explora Mundo Viagens", "Excursões Naturais BR", "Tur BR Destinos",
        "Aventuras do Brasil Turismo", "Tour Cultural BR", "Expedições Tur Brasil", "Maravilhas do Brasil Turismo",
        "Roteiros Brasileiros Excursões", "EcoTurismo Brasil", "BR Viajantes Turismo", "Turismo Praias BR",
        "Brasil Excursões e Viagens", "Natureza Viva Turismo", "Destinos Incríveis BR", "Passeios & Trilhas BR"];


    public function __construct()
    {
    }

    protected function setRandomDelete(IAudit $entity): void
    {
        $entity->setDeletedAt(
            rand(0, 1) ? new DateTime(rand(2020, 2024) . '-' . rand(1, 12) . '-' . rand(1, 28)) : null);
    }

    protected function generateName(): string
    {
        return $this->names[array_rand($this->names)] . rand(1, 1000);
    }

    protected function generateCooperativeName(): string
    {
        return $this->companyName[array_rand($this->companyName)] . rand(1, 1000);
    }

    protected function generateUsername(): string
    {
        return $this->usernames[array_rand($this->usernames)] . rand(1, 1000000);
    }
}