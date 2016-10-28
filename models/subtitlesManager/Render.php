<?php
namespace app\models\subtitlesManager;

class Render {

    static function renderFullSub (array $subtitles) {
        $finalSub = '';
        foreach ($subtitles as $index => $chunk) {
            $text = $chunk['text'];
            $number = $index+1;
            $startTime = Render::renderTime($chunk['startTime']);
            $finishTime = Render::renderTime($chunk['finishTime']);
            $finalSub = $finalSub . $number. PHP_EOL . $startTime. " --> " . $finishTime. PHP_EOL . $text .PHP_EOL . PHP_EOL;
        }
        return $finalSub;
    }

    static function renderTime ($time) {
        //input in ms 1326292, output '00:22:06,292'
        $hours = floor($time/(60*60*1000));
        $hoursString = sprintf("%02d", $hours);
        $minutesString = date('i', $time/1000);
        $secondsString = date('s', $time/1000);
        $ms = $time%1000;
        $msString = sprintf("%03d", $ms);
        return "$hoursString:$minutesString:$secondsString,$msString";
    }

}