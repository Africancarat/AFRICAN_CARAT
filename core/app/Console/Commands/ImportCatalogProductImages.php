<?php

namespace App\Console\Commands;

use App\Models\Item;
use App\Models\Subcategory;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class ImportCatalogProductImages extends Command
{
    protected $signature = 'products:import-from-storage
                            {--dry-run : Preview without writing to the database}
                            {--update-existing : Update items whose image file is missing on disk}';

    protected $description = 'Import catalog items from public/storage/images/products/products';

    private const PRODUCT_DIR = 'products/products';

    public function handle(): int
    {
        $dir = public_path('storage/images/' . self::PRODUCT_DIR);
        if (! is_dir($dir)) {
            $this->error('Product image folder not found: ' . $dir);

            return self::FAILURE;
        }

        $files = collect(scandir($dir))
            ->filter(fn ($f) => $f !== '.' && $f !== '..' && is_file($dir . DIRECTORY_SEPARATOR . $f))
            ->values();

        if ($files->isEmpty()) {
            $this->warn('No image files found.');

            return self::SUCCESS;
        }

        $groups = $this->groupFiles($files);
        $dryRun = (bool) $this->option('dry-run');
        $updateExisting = (bool) $this->option('update-existing');

        $created = 0;
        $updated = 0;
        $skipped = 0;
        $nextId = (int) Item::max('id');

        foreach ($groups as $baseKey => $primaryFile) {
            $relativePhoto = self::PRODUCT_DIR . '/' . $primaryFile;
            $name = $this->humanName($baseKey);
            $slug = Str::slug($name);
            $categoryId = $this->categoryIdFor($baseKey);
            $subcategoryId = $this->subcategoryIdFor($baseKey, $categoryId);

            $existing = Item::where('slug', $slug)->first();

            if ($existing) {
                if ($updateExisting || ! $this->photoExistsOnDisk($existing->photo)) {
                    if (! $dryRun) {
                        $existing->forceFill([
                            'photo' => $relativePhoto,
                            'thumbnail' => $relativePhoto,
                            'status' => 1,
                        ])->saveQuietly();
                    }
                    $this->line("Updated: {$name} → {$primaryFile}");
                    $updated++;
                } else {
                    $skipped++;
                }
                continue;
            }

            if ($dryRun) {
                $this->line("Would create: {$name} [{$slug}] → {$primaryFile}");
                $created++;
                continue;
            }

            $nextId++;
            $item = new Item([
                'category_id' => $categoryId,
                'subcategory_id' => $subcategoryId,
                'childcategory_id' => null,
                'brand_id' => null,
                'tax_id' => 1,
                'name' => $name,
                'slug' => $slug,
                'sku' => Str::upper(Str::random(10)),
                'tags' => '',
                'video' => null,
                'sort_details' => $this->defaultSortDetails($name),
                'specification_name' => null,
                'specification_description' => null,
                'is_specification' => 0,
                'details' => '<p>' . e($this->defaultSortDetails($name)) . '</p>',
                'photo' => $relativePhoto,
                'thumbnail' => $relativePhoto,
                'discount_price' => 1200,
                'previous_price' => 1400,
                'stock' => 25,
                'meta_keywords' => '',
                'meta_description' => $name,
                'status' => 1,
                'is_type' => 'undefine',
                'item_type' => 'normal',
                'date' => null,
                'file' => null,
                'link' => null,
                'file_type' => null,
            ]);
            $item->id = $nextId;
            $item->save();

            $this->line("Created: {$name} → {$primaryFile}");
            $created++;
        }

        if ($updateExisting) {
            $updated += $this->repairOrphanItems($groups, $dryRun);
        }

        $this->newLine();
        $this->info(($dryRun ? '[dry-run] ' : '') . "Created: {$created}, updated: {$updated}, skipped: {$skipped}.");

        return self::SUCCESS;
    }

    /**
     * @param  \Illuminate\Support\Collection<int, string>  $files
     * @return array<string, string> baseKey => primary filename
     */
    private function groupFiles($files): array
    {
        $grouped = [];

        foreach ($files as $file) {
            $base = $this->baseKey($file);
            if (! isset($grouped[$base])) {
                $grouped[$base] = [];
            }
            $grouped[$base][] = $file;
        }

        $result = [];
        foreach ($grouped as $base => $variants) {
            $result[$base] = $this->pickPrimaryFile($variants);
        }

        ksort($result);

        return $result;
    }

    private function baseKey(string $filename): string
    {
        $name = pathinfo($filename, PATHINFO_FILENAME);
        $name = preg_replace('/\s*-\s*Copy$/i', '', $name) ?? $name;
        $name = preg_replace('/-(rose-gold|white-gold|yellow-gold|silver-gold|rose-old|yellow-old)(-\d+)?$/i', '', $name) ?? $name;
        $name = preg_replace('/-\d+$/', '', $name) ?? $name;

        return $name;
    }

  /**
     * @param  array<int, string>  $variants
     */
    private function pickPrimaryFile(array $variants): string
    {
        usort($variants, function ($a, $b) {
            return $this->filePriority($a) <=> $this->filePriority($b);
        });

        return $variants[0];
    }

    private function filePriority(string $file): int
    {
        $score = 0;
        if (preg_match('/\s*-\s*Copy/i', $file)) {
            $score += 100;
        }
        if (preg_match('/-2\.(png|jpg|jpeg|webp)$/i', $file)) {
            $score += 50;
        }
        if (preg_match('/-1\.(png|jpg|jpeg|webp)$/i', $file)) {
            $score -= 10;
        }
        if (preg_match('/-(rose-gold|yellow-gold|white-gold)\.(png|jpg|jpeg|webp)$/i', $file)) {
            $score -= 5;
        }

        return $score;
    }

    private function humanName(string $baseKey): string
    {
        return preg_replace('/\s+/', ' ', ucwords(str_replace('_', ' ', $baseKey))) ?? $baseKey;
    }

    private function categoryIdFor(string $baseKey): int
    {
        $n = strtolower($baseKey);

        if (str_contains($n, 'engagement')) {
            return 29;
        }
        if (str_contains($n, 'earring') || str_contains($n, 'hoop') || str_contains($n, 'stud')) {
            return 30;
        }
        if (str_contains($n, 'necklace') || str_contains($n, 'pendant')) {
            return 31;
        }
        if (str_contains($n, 'bracelet')) {
            return 32;
        }

        return 28;
    }

    private function subcategoryIdFor(string $baseKey, int $categoryId): ?int
    {
        $n = strtolower($baseKey);
        $map = [
            'eternity' => 'Eternity-Rings',
            'engagement' => 'Minimalist-Rings',
            'stud' => 'Stud-Earrings',
            'hoop' => 'Stud-Earrings',
            'pendant' => 'Pendant',
            'necklace' => 'Pendant',
        ];

        foreach ($map as $needle => $slug) {
            if (str_contains($n, $needle)) {
                $sub = Subcategory::where('category_id', $categoryId)->where('slug', $slug)->first();

                return $sub?->id;
            }
        }

        return null;
    }

    private function defaultSortDetails(string $name): string
    {
        return "Hand-crafted {$name} featuring lab-grown diamonds, IGI-certified quality, and Monteluca finishing.";
    }

    private function photoExistsOnDisk(?string $photo): bool
    {
        $photo = ltrim(trim((string) $photo), '/');
        if ($photo === '') {
            return false;
        }

        return is_file(public_path('storage/images/' . $photo));
    }

    /**
     * Match legacy items (broken OM_* paths) to imported groups by normalized name.
     *
     * @param  array<string, string>  $groups
     */
    /** @var array<int, string> item id => filename in products/products */
    private const LEGACY_ITEM_FILES = [
        612 => 'Round_Lab_Grown_Diamond_Twist_Split_Shank_Engagement_Ring-yellow-gold.png',
        613 => 'Round_Lab_Grown_Diamond_Twist_Split_Shank_Engagement_Ring-rose-gold.png',
        614 => 'Round_Lab_Grown_Diamond_Solitaire_Necklace-yellow-gold.png',
        616 => 'Round_Cut_Lab_Grown_Diamond_Halo_Stud_Earrings-rose-gold.png',
        617 => 'Channel_Set_Half_Eternity_Lab_Diamond_Ring-white-gold.png',
        619 => 'Multi_Row_Lab_Grown_Diamond_Wide_Band-rose-gold.png',
        620 => 'Channel_Set_Half_Eternity_Lab_Diamond_Ring-rose-gold.png',
        703 => 'Channel_Set_Half_Eternity_Lab_Diamond_Ring-yellow-gold.png',
    ];

    private function repairOrphanItems(array $groups, bool $dryRun): int
    {
        $fixed = 0;
        $bySlug = [];
        foreach ($groups as $base => $file) {
            $bySlug[Str::slug($this->humanName($base))] = $file;
        }

        Item::query()->orderBy('id')->each(function (Item $item) use ($bySlug, $dryRun, &$fixed) {
            if ($this->photoExistsOnDisk($item->photo)) {
                return;
            }

            $file = self::LEGACY_ITEM_FILES[$item->id] ?? null;
            $slug = $item->slug;
            $file = $file ?? ($bySlug[$slug] ?? null);

            if (! $file) {
                $normalizedItem = strtolower(preg_replace('/[^a-z0-9]+/', '', $item->name) ?? '');
                foreach ($bySlug as $groupSlug => $candidate) {
                    $normalizedGroup = strtolower(preg_replace('/[^a-z0-9]+/', '', str_replace('-', ' ', $groupSlug)) ?? '');
                    if ($normalizedItem !== '' && str_contains($normalizedGroup, substr($normalizedItem, 0, 12))) {
                        $file = $candidate;
                        break;
                    }
                }
            }

            if (! $file) {
                return;
            }

            $relative = self::PRODUCT_DIR . '/' . $file;
            if (! $dryRun) {
                $item->forceFill([
                    'photo' => $relative,
                    'thumbnail' => $relative,
                    'status' => 1,
                ])->saveQuietly();
            }
            $this->line("Repaired orphan: {$item->name} → {$file}");
            $fixed++;
        });

        return $fixed;
    }
}
