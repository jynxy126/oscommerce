<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  namespace osCommerce\OM\Core\Site\Admin\Module\Service;

  use osCommerce\OM\Core\Registry;
  use osCommerce\OM\Core\OSCOM;

  class Currencies {
    var $title,
        $description,
        $uninstallable = false,
        $depends = 'Language',
        $precedes;

    public function __construct() {
      $OSCOM_Language = Registry::get('Language');

      $OSCOM_Language->loadIniFile('modules/services/currencies.php');

      $this->title = OSCOM::getDef('services_currencies_title');
      $this->description = OSCOM::getDef('services_currencies_description');
    }

    public function install() {
      $data = array('title' => 'Use Default Language Currency',
                    'key' => 'USE_DEFAULT_LANGUAGE_CURRENCY',
                    'value' => '-1',
                    'description' => 'Automatically use the currency set with the language (eg, German->Euro).',
                    'group_id' => '6',
                    'use_function' => 'osc_cfg_use_get_boolean_value',
                    'set_function' => 'osc_cfg_set_boolean_value(array(1, -1))');

      OSCOM::callDB('Admin\InsertConfigurationParameters', $data, 'Site');
    }

    public function remove() {
      OSCOM::callDB('Admin\DeleteConfigurationParameters', $this->keys(), 'Site');
    }

    public function keys() {
      return array('USE_DEFAULT_LANGUAGE_CURRENCY');
    }
  }
?>
