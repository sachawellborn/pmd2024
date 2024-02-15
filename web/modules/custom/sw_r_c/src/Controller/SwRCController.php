<?php
declare(strict_types = 1);

namespace Drupal\sw_r_c\Controller;

use Drupal\Core\Controller\ControllerBase;

/**
 * Returns responses for SW Routes and Controllers routes.
 */
final class SwRCController extends ControllerBase {

  /**
   * Builds the response.
   */
  public function __invoke(): array {

    $build['content'] = [
      '#type' => 'item',
      '#markup' => $this->t('It works!'),
    ];

    return $build;
  }

}
