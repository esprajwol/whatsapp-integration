<?php

namespace App\Traits;
use FFMpeg;

trait GenericSpeechRecognition
{
 
  public static function transcribe($wavFileName): string
  {
    $outputFile = self::convertOggToWav($wavFileName);    
    return "this is text";
  }

  public static function convertOggToWav($inputFile): string
  {
    $outputFile = pathinfo($inputFile, PATHINFO_FILENAME). '.wav';
    FFMpeg::fromDisk("voices")->open($inputFile)->export()->inFormat(new \FFMpeg\Format\Audio\Wav)->save($outputFile);
    return $outputFile;
  }
}
