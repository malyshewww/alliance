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

    $post_data = ['action'=>'GoodsFull', 'data'=>['api_key'=>'561157ca-08e5-11e5-811f-001e67acd771', 'articles_array'=>["2000000072784"]]];

    $response = \Drupal::httpClient()->post('https://fereks.ru/api/dealers/?token=561157ca-08e5-11e5-811f-001e67acd771', [
      'json' => $post_data,
      'headers' => [
        'Content-type' => 'application/json',
      ],
    ])->getBody()->getContents();

    $products = json_decode($response, TRUE);
    ksm($products);


    $uid = '651c4e01-3e74-11ea-80c5-6470021075e4';

    $node_loaded_by_uuid = \Drupal::entityTypeManager()
      ->getStorage('node')
      ->loadByProperties([
        'uuid' => $uid,
        'type' => 'modification'
      ]);
    $node_loaded_by_uuid = reset($node_loaded_by_uuid);
    ksm($node_loaded_by_uuid);


    foreach ($node_loaded_by_uuid->field_files as $files){
      $hash[$files->entity->field_file_name->value] = $files->entity->field_hash->value;
    }
    $get_arr = $products['data'][0]['ДоступныеФайлы'];

    if(!empty(array_diff($hash, $get_arr))){
      //если есть различия в хешах
      ;
    }

//
//    $post_data = [
//      'action'=>'GoodsFiles',
//      'data'=>[
//        'api_key'=>'561157ca-08e5-11e5-811f-001e67acd771',
//        'files' => [[
//          'uid' => $uid,
//          'files'=>['Рендер', 'Паспорт']
//        ]]
//      ]
//    ];
//
//    $dir = 'public://fereks/'.$uid;
//    if(!is_dir($dir) ){
//      mkdir($dir);
//    }
//    $real_dir = \Drupal::service('file_system')->realpath($dir);
//    $files = glob($real_dir."/*");
//    if (count($files) > 0) {
//      foreach ($files as $file) {
//        if (file_exists($file)) {
//          unlink($file);
//        }
//      }
//    }
//    $response = \Drupal::httpClient()->post('https://fereks.ru/api/dealers/?token=561157ca-08e5-11e5-811f-001e67acd771', [
//      'json' => $post_data,
//      'headers' => [
//        'Content-type' => 'application/json',
//      ],
//    ])->getBody()->getContents();
//    $file = 'public://fereks/'.$uid.'/file.zip';
//    file_put_contents($file, $response);
//    // get the absolute path to $file
//    $path =  \Drupal::service('file_system')->realpath($file);
//    $zip = new ZipArchive;
//    $res = $zip->open($path);
//    if ($res === TRUE) {
//      // extract it to the path we determined above
//      $zip->extractTo($real_dir);
//      $zip->close();
//      ksm("WOOT! ".$file." extracted to ".$path);
//    } else {
//      ksm("Doh! I couldn't open ". $file);
//    }
//    $files = glob($real_dir."/*.json");
//    if(isset($files[0])) {
//      $json = file_get_contents($files[0]);
//      $json = $this->removeBOM($json);
//      $json_data = json_decode($json, 'TRUE');
//      $json_data = reset($json_data);
//      ksm($json_data);
//    }
//    $image_file_id = NULL;
//    $paragraph_array = [];
//    foreach ($json_data as $name => $value) {
//      if($name == 'Рендер'){
//        $node_loaded_by_uuid->field_images->entity->delete();
//        $file = File::create([
//          'filename' => $value['ИмяФайла'],
//          'uri' => $dir.'/'.$value['ИмяФайла'],
//          'status' => 1,
//          'uid' => 1,
//        ]);
//        $file->save();
//        $image_file_id = $file->id();
//      } else {
//        $file = File::create([
//          'filename' => $value['ИмяФайла'],
//          'uri' => $dir.'/'.$value['ИмяФайла'],
//          'status' => 1,
//          'uid' => 1,
//        ]);
//        $file->save();
//        if(!$node_loaded_by_uuid->get('field_files')->isEmpty()){
//          foreach ($node_loaded_by_uuid->field_files as $paragraph){
//            $paragraph->entity->field_file->entity->delete();
//            $paragraph->entity->delete();
//          }
//        }
//          $paragraph = Paragraph::create([
//            'type' => 'files',
//            'field_file_name' => $name,
//            'field_file' => ['target_id' => $file->id()],
//            'field_hash' => $value['Хеш']
//          ]);
//          $paragraph->save();
//          $paragraph_array[] =
//            [
//              'target_id' => $paragraph->id(),
//              'target_revision_id' => $paragraph->getRevisionId(),
//            ];
//
//      }
//    }
//    if(!empty($image_file_id))
//      $node_loaded_by_uuid->set('field_images', ['target_id' => $image_file_id]);
//    if(!empty($paragraph_array))
//      $node_loaded_by_uuid->set('field_files', $paragraph_array);
//    $node_loaded_by_uuid->save();


//    $file = File::create([
//      'filename' => basename($filepath),
//      'uri' => $dir($filepath),
//      'status' => 1,
//      'uid' => 1,
//    ]);
//    $file->save();
//
//

    //ksm($response);
    //$products = json_decode($response, TRUE);
   // ksm($products);


//    $queue = \Drupal::queue('import_fereks');
//    $queue->deleteQueue();
//    $queue->createQueue();

//    foreach ($products['data'] as $product) {
//     // $queue->createItem(['data' => $product, 'type' => 'product']);
//     // $queue->createItem(['data' => ['uid' => $product['uid'], 'files' => array_keys($product['ДоступныеФайлы'])], 'type' => 'files']);
//    }



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
