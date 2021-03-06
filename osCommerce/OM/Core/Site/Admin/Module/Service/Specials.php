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

  class Specials {
    var $title,
        $description,
        $uninstallable = true,
        $depends,
        $precedes;

    public function __construct() {
      $OSCOM_Language = Registry::get('Language');

      $OSCOM_Language->loadIniFile('modules/services/specials.php');

      $this->title = OSCOM::getDef('services_specials_title');
      $this->description = OSCOM::getDef('services_specials_description');
    }

    public function install() {
      $data = array('title' => 'Special Products',
                    'key' => 'MAX_DISPLAY_SPECIAL_PRODUCTS',
                    'value' => '9',
                    'description' => 'Maximum number of products on special to display',
                    'group_id' => '6');

      OSCOM::callDB('Admin\InsertConfigurationParameters', $data, 'Site');
    }

    public function remove() {
      OSCOM::callDB('Admin\DeleteConfigurationParameters', $this->keys(), 'Site');
    }

    public function keys() {
      return array('MAX_DISPLAY_SPECIAL_PRODUCTS');
    }
  }
?>
