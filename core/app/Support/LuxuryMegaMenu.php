<?php

namespace App\Support;

/**
 * Maps main nav JSON items to full-width mega-menu partials (desktop hover).
 * Order matters: engagement & necklaces before generic “rings” / “diamond”.
 */
final class LuxuryMegaMenu
{
    public static function keyForMenuLink(array $link): ?string
    {
        $raw = strtolower(trim(($link['text'] ?? '').' '.($link['href'] ?? '')));

        if ($raw === '') {
            return null;
        }

        if (str_contains($raw, 'engagement')) {
            return 'engagement';
        }

        if (str_contains($raw, 'necklace')) {
            return 'necklaces';
        }

        if (str_contains($raw, 'earring') || str_contains($raw, 'earing')) {
            return 'earrings';
        }

        if ((str_contains($raw, 'ring') || str_contains($raw, 'wedding')) && ! str_contains($raw, 'engagement')) {
            return 'rings';
        }

        if (str_contains($raw, 'diamond') && ! str_contains($raw, 'guide')) {
            return 'diamonds';
        }

        return null;
    }
}
