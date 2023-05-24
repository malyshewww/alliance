<?php

namespace Drupal\tuman_file_formatter\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Field\FieldItemListInterface;

/**
 * Plugin implementation of the 'Tuman File Formatter Preview' formatter.
 *
 * @FieldFormatter(
 *   id = "tuman_file_formatter_preview",
 *   label = @Translation("Tuman File Formatter Preview"),
 *   field_types = {
 *     "file"
 *   }
 * )
 */
class TumanFileFormatterPreview extends FormatterBase {

  /**
   * {@inheritdoc}
   */
  public function settingsSummary() {
    $summary = [];
    $summary[] = $this->t('Tuman File Formatter Preview');
    return $summary;
  }

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $element = [];

    foreach ($items as $delta => $item) {
      $uri = $item->entity->getFileUri();
      if ($item->getEntity()->field_file_name->value == 'IES' ||
        $item->getEntity()->field_file_name->value == 'Чертеж' ||
        $item->getEntity()->field_file_name->value == 'КСС') {

        $element[$delta] = [
          '#theme' => 'tuman_file_formatter_preview',
          '#url' => \Drupal::service('file_url_generator')
            ->generateString($uri),
          '#extension' => pathinfo($uri)['extension'],
          '#size' => format_size($item->entity->getSize()),
          '#name' => $item->getEntity()->field_file_name->value
        ];
      }
    }
    return $element;
  }

}
