<?php

namespace Drupal\drupalcon_2020_lazy_rendering\Controller;

use Drupal\Core\StringTranslation\StringTranslationTrait;

/**
 * Controller for example 1A.
 *
 * This merely provides a route for the block to show on.
 */
class LazyBuilderBlockController {

  use StringTranslationTrait;

  /**
   * Route callback.
   */
  public function content() {
    $build = [];

    $build['static'] = [
      '#markup' => $this->t("This content is the same for everyone."),
    ];

    return $build;
  }

}
