<?php

namespace App\Console\Commands;

use App\Models\Media;
use Illuminate\Console\Command;

class BackfillMediaDimensions extends Command
{
    protected $signature = 'media:backfill-dimensions';

    protected $description = 'Read image dimensions from files and update Media records';

    public function handle(): int
    {
        $media = Media::query()
            ->whereNull('width')
            ->whereNull('height')
            ->get();

        $updated = 0;
        $skipped = 0;

        foreach ($media as $item) {
            $dims = $item->readDimensionsFromExistingFile();

            if ($dims) {
                $item->update([
                    'width' => $dims['width'],
                    'height' => $dims['height'],
                ]);
                $updated++;
            } else {
                $this->warn("Skipped: {$item->path} (file not found or unreadable)");
                $skipped++;
            }
        }

        $this->info("Done. Updated: {$updated}, Skipped: {$skipped}");

        return self::SUCCESS;
    }
}
