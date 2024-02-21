<?php

declare(strict_types = 1);

namespace Drupal\Tests\drupaleasy_repositories\Functional;

// Use Drupal\field\Entity\FieldConfig;
// use Drupal\field\Entity\FieldStorageConfig;.
use Drupal\Tests\BrowserTestBase;

// Use Drupal\Tests\drupaleasy_repositories\Traits\RepositoryContentTypeTrait;.

/**
 * Test description.
 *
 * @group drupaleasy_repositories
 */
final class AddYmlRepoTest extends BrowserTestBase {
  // Use RepositoryContentTypeTrait;.

  /**
   * {@inheritdoc}
   */
  protected $defaultTheme = 'stark';

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'drupaleasy_repositories',
  ];

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();

    // Configure the tests to use the yml_remote plugin.
    $config = $this->config('drupaleasy_repositories.settings');
    $config->set('repositories_plugins', ['yml_remote' => 'yml_remote']);
    $config->save();

    // Create and login as a Drupal user with permission to access the
    // DrupalEasy Repositories Settings page. This is UID=2 because UID=1 is
    // created by
    // web/core/lib/Drupal/Core/Test/FunctionalTestSetupTrait::installParameters().
    // This root user can be accessed via $this->rootUser.
    $admin_user = $this->drupalCreateUser(['configure drupaleasy repositories']);
    $this->drupalLogin($admin_user);

    // $this->createRepositoryContentType();
    // // Create Repository URL field on the user entity.
    // FieldStorageConfig::create([
    //   'field_name' => 'field_repository_url',
    //   'type' => 'link',
    //   'entity_type' => 'user',
    //   'cardinality' => -1,
    // ])->save();
    // FieldConfig::create([
    //   'field_name' => 'field_repository_url',
    //   'entity_type' => 'user',
    //   'bundle' => 'user',
    //   'label' => 'Repository URL',
    // ])->save();
    // Add the Repository URL t0 the default user form mode.
    // Tell drupal we want this field to appear in this particular view mode
    // There is a service to handle display modes. A service is nothing more
    // than a php class. Below is our first instance of services in the course:
    // Type hint!
    /** @var \Drupal\Core\Entity\EntityDisplayRepository $entity_display_repository */

    $entity_display_repository = \Drupal::service('entity_display.repository');
    $entity_display_repository->getFormDisplay('user', 'user', 'default')
      ->setComponent('field_repository_url', ['type' => 'link_default'])
      ->save();
  }

  /**
   * Test callback.
   *
   * @test
   */
  // Public function testSomething(): void {
  //   $admin_user = $this->drupalCreateUser(['access administration pages']);.
  // $this->drupalLogin($admin_user);
  //   $this->drupalGet('admin');
  //   // $this->assertSession()->elementExists('xpath', '//h1[text() = "Administration"]');
  // }.

  /**
   * Test that the settings page can be reached and works as expected.
   *
   * This tests that an admin user can access the settings page, select a
   * plugin to enable, and submit the page successfully.
   *
   * @return void
   *   Returns nothing.
   *
   * @test
   */
  public function testSettingsPage(): void {

    // Get a handle on the browsing session.
    $session = $this->assertSession();

    // Navigate to the DrupalEasy Repositories Settings page and confirm
    // we can reach it.
    $this->drupalGet('/admin/config/services/repositories');
    // Try this with a 500 status code to see it fail.
    $session->statusCodeEquals(200);

    // Select the "Remote .yml file" checkbox and submit the form.
    $edit = [
      'edit-repositories-plugins-yml-remote' => 'yml_remote',
    ];
    $this->submitForm($edit, 'Save configuration');
    $session->statusCodeEquals(200);
    $session->responseContains('The configuration options have been saved');
    $session->checkboxChecked('edit-repositories-plugins-yml-remote');
    $session->checkboxChecked('edit-repositories-plugins-github');

  }

  /**
   * Test that the settings page cannot be reached without permission.
   *
   * @return void
   *   Returns nothing
   *
   * @test
   *
   * @throws \Behat\Mink\Exception\ExpectationException
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  public function testUnprivilegedSettingsPage(): void {
    $session = $this->assertSession();
    $authenticated_user = $this->drupalCreateUser(['access content']);
    $this->drupalLogin($authenticated_user);
    $this->drupalGet('/admin/config/services/repositories');
    // Test to ensure that the page loads without error.
    // See https://developer.mozilla.org/en-US/docs/Web/HTTP/Status
    $session->statusCodeEquals(403);
  }

}
