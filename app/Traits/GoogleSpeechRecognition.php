<?php

namespace App\Traits;

use Google\Cloud\Speech\V1\SpeechClient;
use Google\Cloud\Speech\V1\RecognitionAudio;
use Google\Cloud\Speech\V1\RecognitionConfig;
use Google\Cloud\Speech\V1\RecognitionConfig\AudioEncoding;

trait GoogleSpeechRecognition
{
    public static function transcribe($wavFilePath)
    {
        try {
            $speechClient = new SpeechClient();

            $audio = (new RecognitionAudio())
                ->setContent(file_get_contents($wavFilePath));

            $config = (new RecognitionConfig())
                ->setEncoding(AudioEncoding::LINEAR16)
                ->setSampleRateHertz(16000)
                ->setLanguageCode('en-US');

            $response = $speechClient->recognize($config, $audio);

            $transcription = '';
            foreach ($response->getResults() as $result) {
                $transcription .= $result->getAlternatives()[0]->getTranscript();
            }
            var_dump($transcription);
            return $transcription;
        } catch (\Exception $err) {
            var_dump($err);
            return "";
        }
    }

}