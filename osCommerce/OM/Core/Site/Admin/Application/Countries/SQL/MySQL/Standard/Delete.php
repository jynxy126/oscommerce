<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  namespace osCommerce\OM\Core\Site\Admin\Application\Countries\SQL\MySQL\Standard;

  use osCommerce\OM\Core\Registry;

  class Delete {
    public static function execute($data) {
      $OSCOM_PDO = Registry::get('PDO');

      $Qcountry = $OSCOM_PDO->prepare('delete from :table_countries where countries_id = :countries_id');
      $Qcountry->bindInt(':countries_id', $data['id']);
      $Qcountry->execute();

      return ( $Qcountry->rowCount() === 1 );
    }
  }
?>
