<?php

namespace Drupal\taxonomy_block_menu\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Cache\Cache;
/**
 * Provides an example block.
 *
 * @Block(
 *   id = "taxonomy_block_menu_term",
 *   admin_label = @Translation("Разделы таксономии с иерархией"),
 *   category = @Translation("taxonomy block menu")
 * )
 */
class TermBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $term = \Drupal::routeMatch()->getParameter('taxonomy_term');

    $aliasManager = \Drupal::service('path_alias.manager');

    $build = [];
    if($term) {

      $child_terms = \Drupal::entityTypeManager()
        ->getStorage('taxonomy_term')
        ->loadTree('product_type', $term->id(), 1, TRUE);



      foreach ($child_terms as $child_term) {
        $alias = $aliasManager->getAliasByPath('/taxonomy/term/' . $child_term->id());
        $term_out[$child_term->id()]['name'] = $child_term->getName();
        $term_out[$child_term->id()]['url'] = $alias;
      }
      if (!empty($term_out)) {
        $build['content'] = [
          '#theme' => 'taxonomy_block_menu',
          '#content' => $term_out,
          '#title' => $term->getName(),
          '#view_mode' => 'parent',

        ];
      }

    }

    return $build;


  }
 public function getCacheTags() {
    //With this when your node change your block will rebuild
    if ($term = \Drupal::routeMatch()->getParameter('taxonomy_term')) {
      //if there is node add its cachetag
      return Cache::mergeTags(parent::getCacheTags(), array('term:' . $term->id()));
    } else {
      //Return default tags instead.
      return parent::getCacheTags();
    }
  }

  public function getCacheContexts() {
    //if you depends on \Drupal::routeMatch()
    //you must set context of this block with 'route' context tag.
    //Every new route this block will rebuild
    return Cache::mergeContexts(parent::getCacheContexts(), array('route'));
  }
}
