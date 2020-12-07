<?php

namespace Drupal\drupalcon_2020_lazy_rendering\Controller;

use Drupal\Core\Security\TrustedCallbackInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;

/**
 * Controller for example 2.
 *
 * This nests render elements with different cacheability.
 */
class MixedCachingController implements TrustedCallbackInterface {

  use StringTranslationTrait;

  /**
   * Route callback.
   */
  public function content() {
    $build = [];

    $build['username'] = [
      '#lazy_builder' => [get_class($this) . '::nameLazyBuilder', []],
    ];
    $build['username']['#cache']['contexts'] = ['user'];

    $build['static'] = [
      '#markup' => $this->t("This content is the same for everyone."),
    ];

    // Attach the library to style the letters.
    $build['#attached']['library'][] = 'drupalcon_2020_lazy_rendering/styles';

    return $build;
  }

  /**
   * Outer lazy builder.
   *
   * This creates the list of letters from the current user's name.
   */
  public static function nameLazyBuilder() {
    $build['name'] = [
      '#type' => 'container',
      '#attributes' => [
        'class' => ['letter-block-container'],
      ],
    ];

    $username = \Drupal::currentUser()->getDisplayName();
    foreach (str_split($username) as $character) {
      $build['name'][] = static::buildLetter($character);
    }

    return $build;
  }

  /**
   * Builds the cacheable render array for a single letter.
   *
   * @param string $character
   *   The letter to user.
   *
   * @return array
   *   The render array.
   */
  public static function buildLetter($character) {
    $build['letter'] = [
      '#type' => 'container',
      '#attributes' => [
        'class' => ['letter-block'],
      ],
    ];

    $alphabet = [
      'a' => 'Alfa',
      'b' => 'Bravo',
      'c' => 'Charlie',
      'd' => 'Delta',
      'e' => 'Echo',
      'f' => 'Foxtrot',
      'g' => 'Golf',
      'h' => 'Hotel',
      'i' => 'India',
      'j' => 'Juliett',
      'k' => 'Kilo',
      'l' => 'Lima',
      'm' => 'Mike',
      'n' => 'November',
      'o' => 'Oscar',
      'p' => 'Papa',
      'q' => 'Quebec',
      'r' => 'Romeo',
      's' => 'Sierra',
      't' => 'Tango',
      'u' => 'Uniform',
      'v' => 'Victor',
      'w' => 'Whiskey',
      'x' => 'X-ray',
      'y' => 'Yankee',
      'z' => 'Zulu',
    ];

    $build['letter']['content'] = [
      '#markup' => ' <span style="margin-right:50em;">' . $alphabet[strtolower($character)] . '</span> ',
    ];

    // Defining a cache key will cause this part of the render array to be
    // cached in cache_render. The cache key depends on the particular letter
    // so each version of this render array for a different letter gets its
    // own cache item.
    $build['letter']['#cache']['keys'] = ['letter-' . $character];

    // We don't need to specify cache contexts because each letter block is the
    // same for everyone. Note that as a minimum, the render cache will consider
    // contexts as being theme, language, and permissions.

    return $build;
  }

  /**
   * {@inheritdoc}
   */
  public static function trustedCallbacks() {
    return ['nameLazyBuilder'];
  }

}
