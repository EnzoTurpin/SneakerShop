<?php
// src/Twig/AppExtension.php
namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class AppExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter('mb_title', [$this, 'mbTitle']),
        ];
    }

    public function mbTitle(string $value): string
    {
        // Convertit la chaîne en "title case" en tenant compte de l'encodage UTF-8
        return mb_convert_case($value, MB_CASE_TITLE, 'UTF-8');
    }
}