<?php

namespace Drupal\drupalcon_2020_lazy_rendering\Controller;

use Drupal\Component\Utility\Crypt;
use Drupal\Core\Security\TrustedCallbackInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;

/**
 * Controller for example 1C.
 *
 * This uses a placeholder token to insert lazily-built content.
 */
class LazyBuilderPlaceholderTokenController implements TrustedCallbackInterface {

  use StringTranslationTrait;

  /**
   * Route callback.
   */
  public function content() {
    $build = [];

    $build['intro'] = [
      '#type' => 'container',
    ];

    // Explicitly create a placeholder so we can use a lazy builder
    // placeholder inside other markup.
    // See http://tech.dichtlog.nl/php/2015/08/03/lazy-builder-callback.html
    // and http://www.noreiko.com/blog/using-lazy-builders-twig-templates.
    // Use the same hashing as PlaceholderGenerator::createPlaceholder().
    $placeholder = Crypt::hashBase64('username');

    $build['static'] = [
      '#markup' => $this->t("This content is the same for everyone except for where we say hello $placeholder!"),
    ];

    // Attach the lazy builder to the placeholder.
    $build['#attached']['placeholders'][$placeholder] = [
      '#lazy_builder' => [get_class($this) . '::lazyBuilder', []],
    ];

    return $build;
  }

  public static function lazyBuilder() {
    // Return the dynamic render element.
    $build = [
      '#markup' => \Drupal::currentUser()->getDisplayName(),
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
