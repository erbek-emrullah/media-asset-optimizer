<?php

namespace App\Jobs;

use App\Models\Image;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class ProcessImageVariants implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;

    protected $image;

    public function __construct(Image $image)
    {
        $this->image = $image;
    }

    public function handle(): void
    {
        $sizes = config('image_sizes.sizes');
        $quality = config('image_sizes.quality');
        $manager = new ImageManager(new Driver());

        $originalPath = storage_path('app/private/' . $this->image->path);

        // dd($originalPath);

        // id'yi 6 haneye tamamla ve 3'erli bol
        $paddedId = str_pad($this->image->id, 6, '0', STR_PAD_LEFT);
        $shardPath = substr($paddedId, 0, 3) . '/' . substr($paddedId, 3, 3);

        $variantDir = storage_path('app/public/variants/' . $shardPath);
        if (!file_exists($variantDir)) {
            mkdir($variantDir, 0755, true);
        }

        foreach ($sizes as $name => $dimensions) {
            $img = $manager->decodePath($originalPath);

            if ($dimensions['height']) {
                $img->scaleDown($dimensions['width'], $dimensions['height']);
            } else {
                $img->scaleDown($dimensions['width']);
            }

            $img->save($variantDir . '/' . $name . '.jpg', $quality);
        }

        // TODO: webp formati da eklenecek
    }
}