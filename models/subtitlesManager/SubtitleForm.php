<?php

namespace app\models\subtitlesManager;
use app\models\subtitlesManager\Parse;
use app\models\subtitlesManager\Render;
use app\models\subtitlesManager\Merge;
use yii;
use yii\base\Model;

class SubtitleForm extends Model{

    public $title;
    public $firstSubtitleFile;
    public $secondSubtitleFile;
    public $finalSubtitle;

    public function rules () {

        return [
            [['firstSubtitleFile', 'secondSubtitleFile', 'title'], 'required'],
            [['firstSubtitleFile', 'secondSubtitleFile'], 'file', 'extensions' => 'srt', 'checkExtensionByMimeType' => false ]
        ];

    }

    public function mergeSubtitles() {
        $firstSubtitleRaw  = file_get_contents($this->firstSubtitleFile->tempName);
        $secondSubtitleRaw  = file_get_contents($this->secondSubtitleFile->tempName);
        $firstSubtitleParsed = Parse::getSubsData($firstSubtitleRaw, 'top');
        $secondSubtitleParsed = Parse::getSubsData($secondSubtitleRaw, 'bottom');
        $merge = new Merge($firstSubtitleParsed, $secondSubtitleParsed);
        $mergedSubtitle = $merge->getMergedSubtitles();
        $this->finalSubtitle = Render::renderFullSub($mergedSubtitle);
    }

    public function sendSubs () {
        $length = strlen($this->finalSubtitle);
        header('Content-Description: File Transfer');
        header('Content-Type: text/plain');//<<<<
        header("Content-Disposition: attachment; filename=$this->title.srt");
        header('Content-Transfer-Encoding: utf-8');
        header('Content-Length: ' . $length);
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Expires: 0');
        header('Pragma: public');
        echo $this->finalSubtitle;
    }

}
