<?php

/**
 * @file
 * Theme settings form for Alliance theme.
 */

/**
 * Implements hook_form_system_theme_settings_alter().
 */
function alliance_form_system_theme_settings_alter(&$form, &$form_state) {

  $form['alliance'] = [
    '#type' => 'details',
    '#title' => t('Alliance'),
    '#open' => TRUE,
  ];

  $form['alliance']['font_size'] = [
    '#type' => 'number',
    '#title' => t('Font size'),
    '#min' => 12,
    '#max' => 18,
    '#default_value' => theme_get_setting('font_size'),
  ];

}
