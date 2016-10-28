<?php
namespace app\models\SubtitlesManager;

class Merge {
    
    public $inputSubtitles;

    function __construct (array $firstSubtitle, array $secondSubtitle) {
        $this->inputSubtitles = array_merge($firstSubtitle, $secondSubtitle);
        uasort($this->inputSubtitles, function ($a, $b) {
            return $a['startTime']-$b['startTime'];
        });
    }

    public function getMergedSubtitles () {
        $mergedSubs[0] = $this->inputSubtitles[0];

        foreach ($this->inputSubtitles as $index => $currentSubtitle) {
            if ($index === 0) continue;

            $previousSubtitle = end($mergedSubs);
            array_pop($mergedSubs);
            $times = array($previousSubtitle['startTime'], $previousSubtitle['finishTime'], $currentSubtitle['startTime'], $currentSubtitle['finishTime']);
            sort($times);

            $firstChunkStart = $times[0];
            $firstChunkFinish = $times[1];
            $firstChunkText = $this->generateSubtitleText($firstChunkStart, $firstChunkFinish, $previousSubtitle, $currentSubtitle);

            $secondChunkStart = $firstChunkFinish;
            $secondChunkFinish = $times[2];
            $secondChunkText = $this->generateSubtitleText($secondChunkStart, $secondChunkFinish, $previousSubtitle, $currentSubtitle);

            $thirdChunkStart = $secondChunkFinish;
            $thirdChunkFinish = $times[3];
            $thirdChunkText = $this->generateSubtitleText($thirdChunkStart, $thirdChunkFinish, $previousSubtitle, $currentSubtitle);

            $mergedSubs[] = $this->createSubtitle($firstChunkStart, $firstChunkFinish, $firstChunkText);
            $mergedSubs[] = $this->createSubtitle($secondChunkStart, $secondChunkFinish, $secondChunkText);
            $mergedSubs[] = $this->createSubtitle($thirdChunkStart, $thirdChunkFinish, $thirdChunkText);

            $mergedSubs = $this->deleteEmptySubs($mergedSubs);
        }
        return $this->deleteSmallSubs($mergedSubs);
    }
    
    public function checkIfChunkValid($startTime, $finishTime, array $subChunk) {
        return $subChunk['finishTime']>=$finishTime && $subChunk['startTime']<=$startTime;
    }



    public function generateSubtitleText($startTime, $finishTime, $firstSub, $secondSub) {
        if ($startTime === $finishTime) {
            return '';
        }
        $firstSubValid = $this->checkIfChunkValid($startTime, $finishTime, $firstSub);
        $secondSubValid = $this->checkIfChunkValid($startTime, $finishTime, $secondSub);
        if ($firstSubValid && $secondSubValid) {
            if ($secondSub['position'] === 'top') {
                return $secondSub['text'] . PHP_EOL . " " . PHP_EOL . $firstSub['text'];
            } else {
                return $firstSub['text'] . PHP_EOL . " " . PHP_EOL . $secondSub['text'];
            }
        } else if ($firstSubValid) {
            return $firstSub['text'];
        } else if ($secondSubValid) {
            return $secondSub['text'];
        }
        return '';
    }

    public function createSubtitle ($startTime, $finishTime, $text) {
        return [
            'startTime' => $startTime,
            'finishTime' => $finishTime,
            'text' => $text
        ];
    }

    public function deleteEmptySubs(array $inputSubs) {
        $outputSubs = array_filter($inputSubs, function ($chunk) {
            return $chunk['text'] !== '';
        });
        return array_values($outputSubs);
    }

    public function deleteSmallSubs (array $inputSubs) {
        $outputSubs = array_filter($inputSubs, function ($chunk) {
            return ($chunk['finishTime']-$chunk['startTime']) > 300;
        });
        return array_values($outputSubs);
    }

}