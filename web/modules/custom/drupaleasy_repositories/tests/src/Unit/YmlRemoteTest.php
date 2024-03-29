<?php

declare(strict_types=1);

namespace Drupal\Tests\drupaleasy_repositories\Unit;

use Drupal\drupaleasy_repositories\Plugin\DrupaleasyRepositories\YmlRemote;
use Drupal\Tests\UnitTestCase;

/**
 * Test description.
 *
 * @group drupaleasy_repositories
 */
final class YmlRemoteTest extends UnitTestCase {

  /**
   * The YmlRemote plugin.
   *
   * @var \Drupal\drupaleasy_repositories\Plugin\DrupaleasyRepositories\YmlRemote
   */
  protected YmlRemote $ymlRemote;

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();
    $this->ymlRemote = new YmlRemote([], 'yml_remote', []);
  }

  /**
   * Tests that our help text is returned as expected.
   *
   * @covers YmlRemote::validateHelpText
   * @test
   */
  public function testValidateHelpText(): void {
    self::assertEquals('https://anything.anything/anything/anything.yml (or http or yaml)', $this->ymlRemote->validateHelpText(), 'Help text does not match');
  }

  /**
   * Data provider for the testValidate() method.
   *
   * @return array<int, array<int, string|bool>>
   *   The array of values to test.
   */
  public function validateProvider(): array {
    return [
      [
        'A test string',
        FALSE,
      ],
      [
        'http://www.mysite.com/anything.yml',
        TRUE,
      ],
      [
        'https://www.mysite.com/anything.yml',
        TRUE,
      ],
      [
        'https://www.mysite.com/anything.yaml',
        TRUE,
      ],
      [
        '/var/www/html/anything.yaml',
        FALSE,
      ],
      [
        'https://www.mysite.com/some%20directory/anything.yml',
        TRUE,
      ],
      [
        'https://www.my-site.com/some%20directory/anything.yaml',
        TRUE,
      ],
      [
        'https://localhost/some%20directory/anything.yaml',
        TRUE,
      ],
      [
        'https://dev.www.mysite.com/anything.yml',
        TRUE,
      ],
    ];
  }

  /**
   * Test that urls are valid.
   *
   * @dataProvider validateProvider
   * @covers YmlRemote::validate
   * @test
   */
  public function testValidate(string $testString, bool $expected): void {
    self::assertEquals($expected, $this->ymlRemote->validate($testString), "Validation of '{$testString}' does not return '{$expected}'.");
  }

  /**
   * Test that a Yml repo file is read correctly.
   *
   * @covers YmlRemote::getRepo
   * @test
   */
  public function testGetRepo(): void {
    $repo = $this->ymlRemote->getRepo(__DIR__ . '/../../assets/' . 'batman-repo.yml');
    $repo = reset($repo);
    self::assertEquals('The Batman repository', $repo['label'], 'Label does not match.');
    self::assertEquals('This is where Batman keeps all his crime-fighting code.', $repo['description'], 'Description does not match.');
    self::assertEquals(36, $repo['num_open_issues'], 'Number of open issues does not match.');
    self::assertEquals('yml_remote', $repo['source'], 'Source does not match.');
  }

}
