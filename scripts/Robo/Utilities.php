<?php

namespace degov\Scripts\Robo;


class Utilities {

  public static function removeCliLineBreaks(string $output): string {
    return str_replace(PHP_EOL, '', $output);
  }

}
