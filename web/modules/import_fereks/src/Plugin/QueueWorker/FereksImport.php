<?php

namespace Drupal\import_fereks\Plugin\QueueWorker;

use Drupal\Core\Queue\QueueWorkerBase;
use Drupal\file\Entity\File;
use Drupal\node\Entity\Node;
use Drupal\paragraphs\Entity\Paragraph;
use ZipArchive;

/**
 * Updates a product stock.
 *
 * @QueueWorker(
 *   id = "import_fereks",
 *   title = @Translation("Import Fereks"),
 *   cron = {"time" = 30}
 * )
 */
class FereksImport extends QueueWorkerBase {

  /**
   * {@inheritdoc}
   */
  public function processItem($info) {
    if ($info['type'] == 'product') {
      $data = $info['data'];
      $node_loaded_by_series = \Drupal::entityTypeManager()
        ->getStorage('node')
        ->loadByProperties([
          'field_series' => $data['Серия'],
          'type' => 'product'
        ]);
      if (!empty($node_loaded_by_series)) {
        $node_loaded_by_series = reset($node_loaded_by_series);
        $id_series = $node_loaded_by_series->id();

      }
      else {
        $node = Node::create([
          'type' => 'product',
          'title' => $data['Серия'],
          'field_series' => $data['Серия'],
          'field_manufacturer' => ['target_id' => 6],
          'field_product_type' => ['target_id' => 1]
        ]);
        $node->save();
        $id_series = $node->id();
      }
      $node_loaded_by_uuid = \Drupal::entityTypeManager()
        ->getStorage('node')
        ->loadByProperties(['uuid' => $data['uid'], 'type' => 'modification']);

      if (!empty($node_loaded_by_uuid)) {
        $node_loaded_by_uuid = reset($node_loaded_by_uuid);
        if($node_loaded_by_uuid->field_hash->value != $data['Хеш'])
          $this->updateNode($node_loaded_by_uuid, $data);
        else {
          $node_loaded_by_uuid->set('field_update', 1);
          $node_loaded_by_uuid->save();
        }
      }
      else {
        $node = Node::create([
          'type' => 'modification',
          'title' => $data['Наименование'],
          'field_product' => ['target_id' => $id_series],
          'uuid' => $data['uid']
        ]);
        $this->updateNode($node, $data);

      }

    }
    if($info['type'] == 'files') {
      $this->updateFiles($info['data']['uid'], $info['data']['files']);
    }
  }
  public function updateNode($node, $data){

    $node->set('field_artikul', $data['Артикул']);
    $node->set('field_update', 1);
    $node->set('field_price', $data['Цены'][0]['РРЦ']);
    $node->set('field_hash', $data['Хеш']);
    $node->set('field_naimenovanie', $data['НаименованиеКириллица']);

    if(!empty($data['ЗаводскаяГарантия']))
      $node->set('field_zavodskaya_garantiya', $data['ЗаводскаяГарантия']);

    if(!empty($data['МассаСветильника']))
      $node->set('field_massa_svetilnika', $data['МассаСветильника']);

    if(!empty($data['ГабаритныеРазмерыУпаковкиСветильника']))
      $node->set('field_gabaritnye_razmery_upakovk', $data['ГабаритныеРазмерыУпаковкиСветильника']);

    if(!empty($data['Ударопрочность']))
      $node->set('field_udaroprochnost', $data['Ударопрочность']);

    if(!empty($data['Крепление']))
      $node->set('field_kreplenie', $data['Крепление']);

    if(!empty($data['МатериалРассеивателя']))
      $node->set('field_material_rasseivatelya', $data['МатериалРассеивателя']);

    if(!empty($data['ВидКлиматическогоИсполнения']))
      $node->set('field_vid_klimaticheskogo_ispoln', $data['ВидКлиматическогоИсполнения']);

    if(!empty($data['ТипКривойСилыСвета']))
      $node->set('field_tip_krivoy_sily_sveta', $data['ТипКривойСилыСвета']);

    if(!empty($data['РесурсРаботыСветильника']))
      $node->set('field_resurs_raboty_svetilnika', $data['РесурсРаботыСветильника']);

    if(!empty($data['ИндексЦветопередачи']))
      $node->set('field_indeks_cvetoperedachi', $data['ИндексЦветопередачи']);

    if(!empty($data['ПульсацииСветовогоПотока']))
      $node->set('field_pulsacii_svetovogo_potoka', $data['ПульсацииСветовогоПотока']);

    if(!empty($data['РодТока']))
      $node->set('field_rod_toka', $data['РодТока']);

    if(!empty($data['ПроизводительСветодиодов']))
      $node->set('field_proizvoditel_svetodiodov', $data['ПроизводительСветодиодов']);

    if(!empty($data['КоличествоСветодиодов']))
      $node->set('field_kolichestvo_svetodiodov', $data['КоличествоСветодиодов']);

    if(!empty($data['КлассЗащиты']))
      $node->set('field_klass_zaschity', $data['КлассЗащиты']);

    if(!empty($data['ПотребляемыйТок']))
      $node->set('field_potreblyaemyy_tok', $data['ПотребляемыйТок']);

    if(!empty($data['КоэффициентМощности']))
      $node->set('field_koefficient_moschnosti', $data['КоэффициентМощности']);

    if(!empty($data['ДиапазонЧастотПитающейСети']))
      $node->set('field_diapazon_chastot_pitayusch', $data['ДиапазонЧастотПитающейСети']);

    if(!empty($data['ЦветоваяТемпература']))
      $node->set('field_cvetovaya_temperatura', $data['ЦветоваяТемпература']);

    if(!empty($data['СтепеньЗащиты']))
      $node->set('field_stepen_zaschity', $data['СтепеньЗащиты']);

    if(!empty($data['ДиапазонНапряженияПитающейСети']))
      $node->set('field_diapazon_napryazheniya_pit', $data['ДиапазонНапряженияПитающейСети']);

    if(!empty($data['ПотребляемаяМощность']))
      $node->set('field_potreblyaemaya_moschnost', $data['ПотребляемаяМощность']);

    if(!empty($data['СрокПроизводства']))
      $node->set('field_srok_proizvodstva', $data['СрокПроизводства']);

    if(!empty($data['ЧастотаПитающейСети']))
      $node->set('field_chastota_pitayuschey_seti', $data['ЧастотаПитающейСети']);

    if(!empty($data['КорпусСветильника']))
      $node->set('field_korpus_svetilnika', $data['КорпусСветильника']);

    if(!empty($data['СветовойПоток']))
      $node->set('field_svetovoy_potok', $data['СветовойПоток']);

    if(!empty($data['РабочийТокСветодиодов']))
      $node->set('field_rabochiy_tok_svetodiodov', $data['РабочийТокСветодиодов']);

    if(!empty($data['НапряжениеПитающейСети']))
      $node->set('field_napryazhenie_pitayuschey_s', $data['НапряжениеПитающейСети']);

    if(!empty($data['ВариантИсполнения']))
      $node->set('field_variant_ispolneniya', $data['ВариантИсполнения']);

    if(!empty($data['Назначение']))
      $node->set('field_naznachenie', $data['Назначение']);

    if(!empty($data['ГабаритныеРазмерыСветильника']))
      $node->set('field_gabaritnye_razmery_svetiln', $data['ГабаритныеРазмерыСветильника']);

    if(!empty($data['ТемператураЭксплуатации']))
      $node->set('field_temperatura_ekspluatacii', $data['ТемператураЭксплуатации']);

    $node->save();
  }
  public function updateFiles($uid, $files) {
    //$uid = '03e316c6-fed2-4b41-806f-6d76e41e2d21';

    $node_loaded_by_uuid = \Drupal::entityTypeManager()
      ->getStorage('node')
      ->loadByProperties([
        'uuid' => $uid,
        'type' => 'modification'
      ]);
    if (!empty($node_loaded_by_uuid)) {
      $node_loaded_by_uuid = reset($node_loaded_by_uuid);

      foreach ($node_loaded_by_uuid->field_files as $node_files) {
        $hash[$node_files->entity->field_file_name->value] = $node_files->entity->field_hash->value;
      }
      if (!empty(array_diff($hash, $files))) {
        //если есть различия в хешах
        $post_data = [
          'action' => 'GoodsFiles',
          'data' => [
            'api_key' => '561157ca-08e5-11e5-811f-001e67acd771',
            'files' => [
              [
                'uid' => $uid,
                'files' => array_keys($files)
              ]
            ]
          ]
        ];

        $dir = 'public://fereks/' . $uid;
        if (!is_dir($dir)) {
          mkdir($dir);
        }
        $real_dir = \Drupal::service('file_system')->realpath($dir);
        $files = glob($real_dir . "/*");
        if (count($files) > 0) {
          foreach ($files as $file) {
            if (file_exists($file)) {
              unlink($file);
            }
          }
        }
        $response = \Drupal::httpClient()
          ->post('https://fereks.ru/api/dealers/?token=561157ca-08e5-11e5-811f-001e67acd771', [
            'json' => $post_data,
            'headers' => [
              'Content-type' => 'application/json',
            ],
          ])
          ->getBody()
          ->getContents();
        $file = 'public://fereks/' . $uid . '/file.zip';
        file_put_contents($file, $response);
        // get the absolute path to $file
        $path = \Drupal::service('file_system')->realpath($file);
        $zip = new ZipArchive;
        $res = $zip->open($path);
        if ($res === TRUE) {
          // extract it to the path we determined above
          $zip->extractTo($real_dir);
          $zip->close();
          ksm("WOOT! " . $file . " extracted to " . $path);
        }
        else {
          ksm("Doh! I couldn't open " . $file);
        }
        $files = glob($real_dir . "/*.json");
        if (isset($files[0])) {
          $json = file_get_contents($files[0]);
          $json = $this->removeBOM($json);
          $json_data = json_decode($json, 'TRUE');
          $json_data = reset($json_data);
          ksm($json_data);
        }
        $image_file_id = NULL;
        $paragraph_array = [];
        foreach ($json_data as $name => $value) {
          if ($name == 'Рендер') {
            if (!$node_loaded_by_uuid->field_images->isEmpty()) {
              $node_loaded_by_uuid->field_images->entity->delete();
            }
            $file = File::create([
              'filename' => $value['ИмяФайла'],
              'uri' => $dir . '/' . $value['ИмяФайла'],
              'status' => 1,
              'uid' => 1,
            ]);
            $file->save();
            $image_file_id = $file->id();
          }
          else {
            $file = File::create([
              'filename' => $value['ИмяФайла'],
              'uri' => $dir . '/' . $value['ИмяФайла'],
              'status' => 1,
              'uid' => 1,
            ]);
            $file->save();
            if (!$node_loaded_by_uuid->get('field_files')->isEmpty()) {
              foreach ($node_loaded_by_uuid->field_files as $paragraph) {
                $paragraph->entity->field_file->entity->delete();
                $paragraph->entity->delete();
              }
            }
            $paragraph = Paragraph::create([
              'type' => 'files',
              'field_file_name' => $name,
              'field_file' => ['target_id' => $file->id()],
              'field_hash' => $value['Хеш']
            ]);
            $paragraph->save();
            $paragraph_array[] =
              [
                'target_id' => $paragraph->id(),
                'target_revision_id' => $paragraph->getRevisionId(),
              ];

          }
        }
        if (!empty($image_file_id)) {
          $node_loaded_by_uuid->set('field_images', ['target_id' => $image_file_id]);
        }
        if (!empty($paragraph_array)) {
          $node_loaded_by_uuid->set('field_files', $paragraph_array);
        }
        $node_loaded_by_uuid->save();
      }
    }
  }
  public function removeBOM($str="") {
    if(substr($str, 0, 3) == pack('CCC', 0xef, 0xbb, 0xbf)) {
      $str = substr($str, 3);
    }
    return $str;
  }

}
