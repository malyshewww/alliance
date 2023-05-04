<?php

/**
 * @file
 * Theme settings form for alliance theme.
 */

use Drupal\Core\Form\FormStateInterface;
use Drupal\file\Entity\File;

/**
 * Implements hook_form_system_theme_settings_alter().
 */
function alliance_form_system_theme_settings_alter(&$form, &$form_state) {

  $form['alliance'] = [
    '#type' => 'details',
    '#title' => 'АльянсФерекс',
    '#open' => TRUE,
    '#weight'=> -50,
    '#description' => t('<b>После изменения данных не забудьте очистить все кэши!</b>'),
  ];

  $form['alliance']['phone'] = [
    '#type' => 'textfield',
    '#title' => 'Телефон',
    '#default_value' => theme_get_setting('phone'),
  ];
  $form['alliance']['mail'] = [
    '#type' => 'textfield',
    '#title' => 'E-mail',
    '#default_value' => theme_get_setting('mail'),
  ];
  $form['alliance']['work'] = [
    '#type' => 'textfield',
    '#title' => 'Режим работы офиса',
    '#default_value' => theme_get_setting('work'),
  ];
  $form['alliance']['address'] = [
    '#type' => 'textfield',
    '#title' => 'Адрес',
    '#default_value' => theme_get_setting('address'),
  ];
  $form['alliance']['uraddress'] = [
    '#type' => 'textfield',
    '#title' => 'Юр. Адрес',
    '#default_value' => theme_get_setting('uraddress'),
  ];
  $form['alliance']['inn'] = [
    '#type' => 'textfield',
    '#title' => 'ИНН',
    '#default_value' => theme_get_setting('inn'),
  ];
  $form['alliance']['nameorg'] = [
    '#type' => 'textfield',
    '#title' => 'Название организации',
    '#default_value' => theme_get_setting('nameorg'),
  ];
  $form['alliance']['ogrn'] = [
    '#type' => 'textfield',
    '#title' => 'ОГРН',
    '#default_value' => theme_get_setting('ogrn'),
  ];
  $form['alliance']['telegram'] = [
    '#type' => 'textfield',
    '#title' => 'Telegram',
    '#default_value' => theme_get_setting('telegram'),
  ];
  $form['alliance']['whatsapp'] = [
    '#type' => 'textfield',
    '#title' => 'WhatsApp',
    '#default_value' => theme_get_setting('whatsapp'),
  ];
  $form['alliance']['vk'] = [
    '#type' => 'textfield',
    '#title' => 'vk.com',
    '#default_value' => theme_get_setting('vk'),
  ];
  $form['alliance']['file'] = [
    '#type' => 'managed_file',
    '#title' => 'Карточка организации',
    '#required' => TRUE,
    '#default_value' => theme_get_setting('file'),
    '#upload_location' => 'public://alliance/'

  ];
  $form['#submit'][] = '_alliance_form_system_theme_settings_submit';

}
function _alliance_form_system_theme_settings_submit(&$form, FormStateInterface &$form_state, $form_id = NULL) {
  $image_id = $form_state->getValue('file');
  $image_id = reset($image_id);
  $image_file = File::load($image_id);
  $image_file->setPermanent();
  $image_file->save();
}
