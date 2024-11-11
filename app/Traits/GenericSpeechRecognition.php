<?php

namespace App\Traits;

use Google\Cloud\Speech\V1\SpeechClient;
use Google\Cloud\Speech\V1\RecognitionAudio;
use Google\Cloud\Speech\V1\RecognitionConfig;
use Google\Cloud\Speech\V1\RecognitionConfig\AudioEncoding;

trait GenericSpeechRecognition
{
  public static function transcribe($audioFilePath): string
  {
    return "this is text";
  }

}