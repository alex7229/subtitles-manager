<?php
namespace app\models\SubtitlesManager;

class Parse {
    
    static function getSubsData ($rawContent, $position) {
        preg_match_all("/(\\d+)\\R(\\d{2}:\\d{2}:\\d{2},\\d{3}) --> (\\d{2}:\\d{2}:\\d{2},\\d{3})\\R((.|\\n)+?)\\n\\R/", $rawContent, $matches);
        $parsedSubtitle = [];
        for ($i=0; $i<count($matches[0]); $i++) {
            $subtitleData = [
                'startTime' => Parse::convertTimeToMS($matches[2][$i]),
                'finishTime' => Parse::convertTimeToMS($matches[3][$i]),
                'text' => $matches[4][$i],
                'position' => $position
            ];
            $parsedSubtitle[] = $subtitleData;
        }
        return $parsedSubtitle;
    }

    static function convertTimeToMS ($time) {
        preg_match("/(\\d{2}):(\\d{2}):(\\d{2}),(\\d{3})/", $time, $result);
        $hours = $result[1];
        $minutes = $result[2];
        $seconds = $result[3];
        $ms = $result[4];
        return $ms+$seconds*1000+$minutes*1000*60+$hours*1000*60*60;
        //input '00:22:06,292', output 1326292
    }
}