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
 * id = "portfolio_search_box",
 * admin_label = @Translation("Search Box"),
 * category = @Translation("Portfolio")
 * )
 */
class PortfolioSearchBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $build = array();

    $build['form'] = \Drupal::formBuilder()->getForm('Drupal\features_master\Form\PortfolioSearchForm');

    return $build;
  }
}
