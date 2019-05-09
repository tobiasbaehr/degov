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

  /**
   * @dataProvider wrongNodeVersionDataProvider
   * @expectedException \degov\Scripts\Robo\Exception\ApplicationRequirementFail
   */
  public function testCheckApplicationRequirementFail(string $wrongVersion): void {
    Utilities::checkNodeVersion($wrongVersion);
  }

  /**
   * @dataProvider correctNodeVersionDataProvider
   */
  public function testCheckNoApplicationRequirementFail(string $wrongVersion): void {
    self::assertInternalType('null', Utilities::checkNodeVersion($wrongVersion));
  }

  public function wrongNodeVersionDataProvider(): array {
    return [
      ['v9.11.1'],
      ['9.11.1'],
      ['blub9.11.1'],
    ];
  }

  public function correctNodeVersionDataProvider(): array {
    return [
      ['v6.22.1'],
      ['6.34.5'],
      ['blub6.34.5'],
    ];
  }

}
