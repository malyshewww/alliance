<?php

namespace Drupal\alliance_filter_project\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Cache\Cache;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\taxonomy\Entity\Term;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides an example block.
 *
 * @Block(
 *   id = "alliance_filter_project_example",
 *   admin_label = @Translation("Filter"),
 *   category = @Translation("alliance filter project")
 * )
 */
class ExampleBlock extends BlockBase implements ContainerFactoryPluginInterface{
  /**
   * Drupal\Core\Routing\RouteMatchInterface definition.
   *
   * @var \Drupal\Core\Routing\RouteMatchInterface
   */
  protected $routeMatch;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    $instance = new static($configuration, $plugin_id, $plugin_definition);
    $instance->routeMatch = $container->get('current_route_match');
    return $instance;
  }
  /**
   * {@inheritdoc}
   */
  public function build() {
    $arguments = $this->routeMatch->getParameters()->all();
    if($arguments['view_id'] == 'project' && !empty($arguments['arg_0'])){
      $tid = $arguments['arg_0'];
      $query = \Drupal::database()->select('node_field_data', 'nfd');
      $query->join('node__field_product_type', 'pt', 'pt.entity_id = nfd.nid');
      $query->condition('nfd.type', 'projects');
      $query->condition('pt.bundle', 'projects');
      $query->condition('pt.field_product_type_target_id', $tid);
      $query->join('node__field_filtry', 'f', 'f.entity_id = nfd.nid');
      $query->addField('f', 'field_filtry_target_id');
      $query->groupBy('f.field_filtry_target_id');
      $current_tids = $query->execute()->fetchAllAssoc('field_filtry_target_id');
      $out = [];
      foreach ($current_tids as $current_tid) {
        $parent = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadParents($current_tid->field_filtry_target_id);
        $parent_term =  reset($parent);
        $term = Term::load($current_tid->field_filtry_target_id);
        $out[$parent_term->id()]['name'] = $parent_term->label();
        $out[$parent_term->id()]['child'][] = ['tid' => $term->id(), 'name' => $term->label()];
      }
      $build['content'] = [
        '#theme' => 'alliance_filter_project',
        '#term' => $out,
      ];
      return $build;
    }



  }

  public function getCacheTags() {
    //With this when your node change your block will rebuild
    if ($node = \Drupal::routeMatch()->getParameter('node')) {
      //if there is node add its cachetag
      return Cache::mergeTags(parent::getCacheTags(), array('node:' . $node->id()));
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
