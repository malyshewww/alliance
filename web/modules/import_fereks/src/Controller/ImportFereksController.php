<?php

namespace Drupal\import_fereks\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\file\Entity\File;
use Drupal\paragraphs\Entity\Paragraph;
use ZipArchive;

/**
 * Returns responses for Import Fereks routes.
 */
class ImportFereksController extends ControllerBase {

  /**
   * Builds the response.
   */
  public function build() {


    //import_fereks_start();


    $build['content'] = [
      '#type' => 'item',
      '#markup' => $this->t('It works!'),
    ];

    return $build;
  }
  public function removeBOM($str="") {
    if(substr($str, 0, 3) == pack('CCC', 0xef, 0xbb, 0xbf)) {
      $str = substr($str, 3);
    }
    return $str;
  }
}
