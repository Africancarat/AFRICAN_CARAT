<?php

namespace App\Support;

/**
 * Canonical slugs for /education/guides/{slug} pillar pages (12–15 SEO landing pages).
 */
final class EducationPillarRegistry
{
    /**
     * @return array<string, array{title: string, description: string, tag: string}>
     */
    public static function definitions(): array
    {
        return [
            'diamond-cut' => [
                'title' => 'Diamond Cut — Sparkle & Proportions',
                'description' => 'Understand GIA Excellent / Very Good grading, brilliance, fire, and why cut is often the strongest driver of visible beauty.',
                'tag' => 'The 4Cs',
            ],
            'diamond-colour' => [
                'title' => 'Diamond Colour — From D to Z',
                'description' => 'The GIA D–Z scale, near-colourless sweet spots, and how mounting metal affects face-up hue.',
                'tag' => 'The 4Cs',
            ],
            'diamond-clarity' => [
                'title' => 'Diamond Clarity — Inclusions & Eye-Clean',
                'description' => 'From Flawless to I categories: what magnification reveals versus what your eye sees.',
                'tag' => 'The 4Cs',
            ],
            'diamond-carat' => [
                'title' => 'Diamond Carat — Weight & Spread',
                'description' => 'Points, decimal carats, “magic weights,” face-up measurements, and value beyond the headline number.',
                'tag' => 'The 4Cs',
            ],
            'diamond-shapes' => [
                'title' => 'Diamond Shapes & Anatomy',
                'description' => 'Round brilliant vs princess, emerald, oval, cushion, radiant: brilliance vs size illusion and setting suitability.',
                'tag' => 'Styles',
            ],
            'ring-settings' => [
                'title' => 'Engagement Ring Settings',
                'description' => 'Solitaire, pavé, halo, bezel, cathedral, tension: durability, upkeep, and how each frames the centre stone.',
                'tag' => 'Settings',
            ],
            'metal-types' => [
                'title' => 'Precious Metals for Fine Jewelry',
                'description' => 'Platinum versus 14K / 18K white, yellow, and rose gold: wear, plating, hypoallergenic notes, care.',
                'tag' => 'Materials',
            ],
            'certification-explained' => [
                'title' => 'Grading Certificates & Laboratories',
                'description' => 'What a certificate proves, labs you may encounter, and how verification links combat substitution.',
                'tag' => 'Trust',
            ],
            'fluorescence' => [
                'title' => 'Diamond Fluorescence',
                'description' => 'Blue fluorescence under UV, impact on hue in sunlight, resale perception, and when it can help.',
                'tag' => 'Advanced',
            ],
            'polish-and-symmetry' => [
                'title' => 'Polish & Symmetry',
                'description' => 'Finish quality, facet junctions, hearts & arrows folklore, interplay with overall cut.',
                'tag' => 'Advanced',
            ],
            'budget-and-value' => [
                'title' => 'Budgeting for a Diamond',
                'description' => 'Balancing specs, diminishing returns vs carat tiers, prioritising visible beauty over spreadsheets.',
                'tag' => 'Buying',
            ],
            'engagement-ring-guide' => [
                'title' => 'Engagement Ring Buying Guide',
                'description' => 'Timeline, ring size hints, lifestyles, stacking wedding bands later, aftercare appointments.',
                'tag' => 'Buying',
            ],
            'necklaces-and-pendants' => [
                'title' => 'Necklaces & Pendants — Length & Layers',
                'description' => 'Chains, clasps, layer lengths for solitaire pendants, office vs occasion wear.',
                'tag' => 'Fine jewelry',
            ],
            'earrings-guide' => [
                'title' => 'Diamond Earring Styles',
                'description' => 'Stud mechanics, hoops, drops; secure backs & maintenance for daily wear.',
                'tag' => 'Fine jewelry',
            ],
            'wedding-bands-guide' => [
                'title' => 'Wedding Bands That Pair Cleanly',
                'description' => 'Contouring to engagement rings, metal matching, engraving, resizing expectations.',
                'tag' => 'Fine jewelry',
            ],
        ];
    }

    /** @return list<string> */
    public static function slugs(): array
    {
        return array_keys(self::definitions());
    }
}
