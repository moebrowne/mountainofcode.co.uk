<?php

declare(strict_types=1);

namespace MoeBrowne;

require __DIR__ . '/../vendor/autoload.php';

use Random\Engine\Xoshiro256StarStar;
use Random\Randomizer;

class MetaImage
{
    private \GdImage $image;
    private int $imageWidth;
    private int $imageHeight;
    private int $sectorWidth = 28;
    private int $sectorHeight = 35;
    private Randomizer $randomizer;

    public function __construct(string $title, string $url, int $canvasWidth, int $canvasHeight)
    {
        // Strip emoji because the font doesn't support it
        $title = preg_replace('/[^\x20-\x7E]/u', '', $title);

        $this->imageWidth = $canvasWidth;
        $this->imageHeight = $canvasHeight;
        $this->randomizer = new Randomizer(new Xoshiro256StarStar(md5($title)));
        $this->image = imagecreatetruecolor($this->imageWidth, $this->imageHeight);

        $this->drawSectors();
        $this->drawTitle($title);
        $this->drawUrl($url);
    }

    public function output(): void
    {
        imagepng($this->image);
    }

    private function drawSectors(): void
    {
        $coordX = 0;
        $coordY = 0;

        while ($coordY < $this->imageHeight) {
            while ($coordX < $this->imageWidth) {
                $this->drawSector($coordX, $coordY);
                $coordX += $this->sectorWidth;
            }

            $coordX = 0;
            $coordY += $this->sectorHeight;
        }
    }

    private function drawSector(int $coordX, int $coordY): void
    {
        imagefilledrectangle(
            $this->image,
            $coordX,
            $coordY,
            $coordX + $this->sectorWidth,
            $coordY + $this->sectorHeight,
            $this->randomizer->getInt(0, 100) <= 2 ? $this->randomCorruptColour() : $this->randomOkColour(),
        );
    }

    private function drawTitle(string $title): void
    {
        $maxWidth = $this->imageWidth - 80;
        $words = explode(' ', $title);

        $titleWithBreaks = [];

        foreach ($words as $word) {
            $lastLine = array_last(explode(PHP_EOL, implode(' ', $titleWithBreaks)));

            $lastLineLengthWithNextWord = $this->getWidthOfText(trim($lastLine . ' ' . $word), 80);

            if ($lastLineLengthWithNextWord >= $maxWidth) {
                $titleWithBreaks[] = PHP_EOL . $word;
            } else {
                $titleWithBreaks[] = $word;
            }
        }

        $titleWithBreaks = implode(' ', $titleWithBreaks);

        imagettftext(
            $this->image,
            80,
            0,
            60,
            140,
            imagecolorallocate($this->image, 85, 85, 85),
            __DIR__ . '/../public/assets/fonts/Ubuntu-Regular.ttf',
            $titleWithBreaks,
        );
    }

    private function drawUrl(string $url): void
    {
        imagefilledrectangle(
            $this->image,
            0,
            $this->imageHeight - 56,
            $this->imageWidth,
            $this->imageHeight,
            imagecolorallocate($this->image, 235, 235, 235),
        );

        imagettftext(
            $this->image,
            20,
            0,
            $this->imageWidth - 20 - $this->getWidthOfText($url, 20),
            $this->imageHeight - 20,
            imagecolorallocate($this->image, 85, 85, 85),
            __DIR__ . '/../public/assets/fonts/Ubuntu-Regular.ttf',
            $url
        );
    }

    private function getWidthOfText(string $text, int $size): int
    {
        $boundingBox = imagettfbbox(
            $size,
            0,
            __DIR__ . '/../public/assets/fonts/Ubuntu-Regular.ttf',
            $text,
        );

        return $boundingBox[4] - $boundingBox[6];
    }

    private function randomOkColour(): int
    {
        $colours = [
            imagecolorallocate($this->image, 221, 223, 222),
            imagecolorallocate($this->image, 231, 233, 230),
            imagecolorallocate($this->image, 242, 242, 242),
            imagecolorallocate($this->image, 234, 234, 234),
            imagecolorallocate($this->image, 235, 235, 235),
            imagecolorallocate($this->image, 232, 232, 232),
            imagecolorallocate($this->image, 229, 229, 229),
            imagecolorallocate($this->image, 224, 224, 224),
            imagecolorallocate($this->image, 237, 237, 237),
            imagecolorallocate($this->image, 225, 225, 223),
        ];

        return $colours[$this->randomizer->getInt(0, count($colours) - 1)];
    }

    private function randomCorruptColour(): int
    {
        $colours = [
            imagecolorallocate($this->image, 237, 206, 209),
        ];

        return $colours[$this->randomizer->getInt(0, count($colours) - 1)];
    }
}
