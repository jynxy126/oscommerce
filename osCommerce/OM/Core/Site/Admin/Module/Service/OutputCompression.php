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

  class OutputCompression {
    var $title,
        $description,
        $uninstallable = true,
        $depends,
        $precedes = 'Session';

    public function __construct() {
      $OSCOM_Language = Registry::get('Language');

      $OSCOM_Language->loadIniFile('modules/services/output_compression.php');

      $this->title = OSCOM::getDef('services_output_compression_title');
      $this->description = OSCOM::getDef('services_output_compression_description');
    }

    public function install() {
      $data = array('title' => 'GZIP Compression Level',
                    'key' => 'SERVICE_OUTPUT_COMPRESSION_GZIP_LEVEL',
                    'value' => '5',
                    'description' => 'Set the GZIP compression level to this value (0=min, 9=max).',
                    'group_id' => '6',
                    'set_function' => 'osc_cfg_set_boolean_value(array(\'0\', \'1\', \'2\', \'3\', \'4\', \'5\', \'6\', \'7\', \'8\', \'9\'))');

      OSCOM::callDB('Admin\InsertConfigurationParameters', $data, 'Site');
    }

    public function remove() {
      OSCOM::callDB('Admin\DeleteConfigurationParameters', $this->keys(), 'Site');
    }

    public function keys() {
      return array('SERVICE_OUTPUT_COMPRESSION_GZIP_LEVEL');
    }
  }
?>
