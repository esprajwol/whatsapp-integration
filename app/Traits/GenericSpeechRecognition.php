<?php

namespace App\Traits;

use FFMpeg;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

trait GenericSpeechRecognition
{

  public static function transcribe($wavFileName): string
  {
    $outputFile = self::convertOggToWav($wavFileName);
    $outputText = self::runPythonScript(base_path("python-scripts/voice-texter.py"), storage_path("voices/" . $outputFile));
    return $outputText;
  }

  private static function convertOggToWav($inputFile): string
  {
    $outputFile = pathinfo($inputFile, PATHINFO_FILENAME) . '.wav';
    FFMpeg::fromDisk("voices")->open($inputFile)->export()->inFormat(new \FFMpeg\Format\Audio\Wav)->save($outputFile);
    return $outputFile;
  }

  public static function runPythonScript($fileName, $argument): string
  {
    $process = new Process(['python3', $fileName, $argument]);
    $process->run();

    if (!$process->isSuccessful()) {
      throw new ProcessFailedException($process);
      return "";
    }
    $output = $process->getOutput();
    return $output;
  }
}
