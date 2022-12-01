<?php


namespace Modules\KctAdmin\Services\BusinessServices\factory;


use League\ColorExtractor\Color;
use League\ColorExtractor\Palette;
use Modules\KctAdmin\Services\BusinessServices\IColorExtractService;

class LeagueColorExtService implements IColorExtractService {

    /**
     * @inheritDoc
     */
    public function getMainColors($image){
        $palette = Palette::fromFilename($image);
        $fiveMainColors = $palette->getMostUsedColors(5);
        $i = -1;
        foreach($fiveMainColors as $color => $count) {
            $i++;
            // colors are represented by integers
            $mainColors[] = Color::fromIntToRgb($color);
            if ($i < 5){
                $mainColors[$i]['a'] = 1;
            }
        }
        return $mainColors;
    }

}
