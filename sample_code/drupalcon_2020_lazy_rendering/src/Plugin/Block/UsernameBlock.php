<?php

namespace Drupal\drupalcon_2020_lazy_rendering\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides a block to greet the user.
 *
 * @Block(
 *   id = "drupalcon_2020_lazy_rendering_username_block",
 *   admin_label = @Translation("DrupalCon 2020 Username"),
 * )
 */
class UsernameBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    // This block returns content that is unique to each user.
    // By default, Drupal caches pages per-role. So unless we do something about
    // it, the cached render array will contain the name of the first user who
    // visited the page, and every user after that will see the wrong name!
    return [
      '#markup' => $this->t('Hello @username', [
        // Note that the service would normally be injected. It's left as a
        // direct call for simplicity.
        '@username' => \Drupal::currentUser()->getDisplayName(),
      ]),
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheContexts() {
    // Set the cache context to 'user' because each user should see a different
    // version of this block.
    return ['user'];
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheTags() {
    // Set the cache tag to 'user:ID' as when a user entity is edited, the
    // username might be changed, and so the cache should be invalidated.
    return ['user:' . \Drupal::currentUser()->id()];
  }

}
