<?php

namespace App\Traits;

use Google\Cloud\Speech\V1\SpeechClient;
use Google\Cloud\Speech\V1\RecognitionAudio;
use Google\Cloud\Speech\V1\RecognitionConfig;
use Google\Cloud\Speech\V1\RecognitionConfig\AudioEncoding;

trait GoogleSpeechRecognition
{
  public static function transcribe($audioFilePath)
  {
    var_dump("audioFilePath: ", $audioFilePath);

    //  try {
    $connectionConfig = [
      'credentials' => base_path(env("GOOGLE_APPLICATION_CREDENTIALS")),

    ];
    $speechClient = new SpeechClient($connectionConfig);

    $audio = (new RecognitionAudio())
      ->setContent(file_get_contents($audioFilePath));

    $config = (new RecognitionConfig())
      ->setEncoding(AudioEncoding::OGG_OPUS)
      ->setSampleRateHertz(12000)->setEnableWordTimeOffsets(true)
      ->setLanguageCode('en-IN')->setModel('default');

    $response = $speechClient->recognize($config, $audio);

    var_dump("audioFilePath: ", $response->getResults());
    $transcription = '';
    foreach ($response->getResults() as $result) {
      $alternatives = $result->getAlternatives();
      $mostLikely = $alternatives[0];
      $transcript = $mostLikely->getTranscript();
      $confidence = $mostLikely->getConfidence();
      printf('Transcript: %s' . PHP_EOL, $transcript);
      printf('Confidence: %s' . PHP_EOL, $confidence);
      $transcription = $mostLikely;
    }
   
    return $transcription;

    // } catch (\Exception $err) {
    //     var_dump($err);
    // }
    //  } finally {
    //    $client->close();
    //    return "";
    //  }
  }

}