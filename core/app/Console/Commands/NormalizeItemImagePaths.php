<?php

namespace App\Console\Commands;

use App\Helpers\ImageHelper;
use App\Models\Item;
use Illuminate\Console\Command;

class NormalizeItemImagePaths extends Command
{
    protected $signature = 'items:normalize-image-paths';

    protected $description = 'Fix double-prefixed photo/thumbnail URLs in the items table';

    public function handle(): int
    {
        $updated = 0;

        Item::query()
            ->select(['id', 'photo', 'thumbnail'])
            ->orderBy('id')
            ->chunkById(100, function ($items) use (&$updated) {
                foreach ($items as $item) {
                    $changes = [];

                    foreach (['photo', 'thumbnail'] as $col) {
                        $raw = $item->getRawOriginal($col);
                        if (! is_string($raw) || trim($raw) === '') {
                            continue;
                        }
                        $fixed = ImageHelper::normalizeStorageImagePath(trim($raw));
                        if ($fixed !== $raw) {
                            $changes[$col] = $fixed;
                        }
                    }

                    if ($changes !== []) {
                        $item->forceFill($changes)->saveQuietly();
                        $updated++;
                    }
                }
            });

        $this->info("Normalized image paths on {$updated} item(s).");

        return self::SUCCESS;
    }
}
