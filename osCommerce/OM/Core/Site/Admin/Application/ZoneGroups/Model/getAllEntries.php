<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  namespace osCommerce\OM\Core\Site\Admin\Application\ZoneGroups\Model;

  use osCommerce\OM\Core\OSCOM;

  class getAllEntries {
    public static function execute($group_id) {
      $data = array('group_id' => $group_id);

      return OSCOM::callDB('Admin\ZoneGroups\EntryGetAll', $data);
    }
  }
?>
