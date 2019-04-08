<?php

namespace degov\Scripts\Robo\Test;

use degov\Scripts\Robo\Utilities;
use PHPUnit\Framework\TestCase;


class UtilitiesTest extends TestCase {

  public function testRemoveCliLineBreaks(): void {
    $text = <<<EOT
Somewhat with 

a

 line


 break.
EOT;

    self::assertEquals('Somewhat with a line break.', Utilities::removeCliLineBreaks($text));
  }

}
