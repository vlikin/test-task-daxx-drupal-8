<?php
/**
 * @file
 * Contains \Drupal\custom\Plugin\Block\CustomBlock.
 */
namespace Drupal\features_master\Plugin\Block;
use Drupal\Core\Block\BlockBase;
/**
 * Provides Custom Block.
 *
 * @Block(
 * id = "my_custom_block",
 * admin_label = @Translation("Custom Block"),
 * category = @Translation("Blocks")
 * )
 */
class PortfolioSearchBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $build = array();

    $build['#markup'] = '' . t('My Custom Form') . '';
    $build['form'] = \Drupal::formBuilder()->getForm('Drupal\features_master\Form\PortfolioSearchForm');

    return $build;
  }
}
