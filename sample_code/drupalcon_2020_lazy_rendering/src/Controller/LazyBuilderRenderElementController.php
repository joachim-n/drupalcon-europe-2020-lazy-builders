<?php

namespace Drupal\drupalcon_2020_lazy_rendering\Controller;

use Drupal\Core\Security\TrustedCallbackInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;

/**
 * Controller for example 1B.
 *
 * This uses a render element with a lazy builder for dynamic content.
 */
class LazyBuilderRenderElementController implements TrustedCallbackInterface {

  use StringTranslationTrait;

  /**
   * Route callback.
   */
  public function content() {
    $build = [];

    $build['intro'] = [
      '#type' => 'container',
    ];

    // This render element is dynamic, so its creation is deferred to a lazy
    // builder.
    $build['intro']['content'] = [
      '#lazy_builder' => [get_class($this) . '::lazyBuilder', []],
    ];
    // Specify how the content that the lazy builder returns should be cached.
    // Because the caching is more specific than the rest of the page (which
    // defaults to 'user:role'), the render system will create a placeholder in
    // the cached HTML.
    $build['intro']['content']['#cache']['contexts'] = ['user'];

    // By default, the result of the lazy builder is not cached at all.
    // However, we can set it to be cached by specifying a cache key. This is
    // useful if the content of the lazy builder is something that can be cached
    // even if it's more dynamic than the rest of the page.
    // $build['intro']['content']['#cache']['keys'] = ['some-cache-key'];

    $build['static'] = [
      '#markup' => $this->t("This content is the same for everyone."),
    ];

    return $build;
  }

  public static function lazyBuilder() {
    // Return the dynamic render element.
    $build = [
      '#markup' => t('Hello @username', [
        '@username' => \Drupal::currentUser()->getDisplayName(),
      ]),
    ];

    return $build;
  }

  /**
   * {@inheritdoc}
   */
  public static function trustedCallbacks() {
    return ['lazyBuilder'];
  }

}
