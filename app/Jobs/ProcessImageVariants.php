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
        $originalPath = storage_path('app/private/' . $this->image->path);

        $paddedId = str_pad($this->image->id, 6, '0', STR_PAD_LEFT);
        $shardPath = substr($paddedId, 0, 3) . '/' . substr($paddedId, 3, 3);

        $variantDir = storage_path('app/public/variants/' . $shardPath);
        if (!file_exists($variantDir)) {
            mkdir($variantDir, 0755, true);
        }

        if ($this->image->mime_type === 'image/gif') {
            $this->convertGifToMp4($originalPath, $variantDir);
            return;
        }

        $sizes = config('image_sizes.sizes');
        $quality = config('image_sizes.quality');
        $manager = new ImageManager(new Driver());

        foreach ($sizes as $name => $dimensions) {
            $img = $manager->decodePath($originalPath);

            if ($dimensions['height']) {
                $img->scaleDown($dimensions['width'], $dimensions['height']);
            } else {
                $img->scaleDown($dimensions['width']);
            }

            $img->save($variantDir . '/' . $name . '.jpg', $quality);
        }
    }

    private function convertGifToMp4($inputPath, $outputDir)
    {
        $gifConfig = config('image_sizes.gif');
        $crf = $gifConfig['crf'];
        $maxWidth = $gifConfig['max_width'];

        $outputPath = $outputDir . '/video.mp4';

        $command = "ffmpeg -i {$inputPath} "
            . "-vf \"scale='min({$maxWidth},iw)':-2\" "
            . "-vcodec libx264 -crf {$crf} "
            . "-movflags faststart -pix_fmt yuv420p "
            . "-an -y {$outputPath} 2>&1";

        exec($command, $output, $exitCode);

        if ($exitCode !== 0) {
            \Log::error('FFmpeg donusum hatasi', ['output' => implode("\n", $output)]);
        }
    }
}