<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  use osCommerce\OM\Core\HTML;
  use osCommerce\OM\Core\OSCOM;

  $Qcountries = $OSCOM_PDO->query('select countries_id, countries_name from :table_countries where countries_id in ("' . implode('", "', array_unique(array_filter(array_slice($_POST['batch'], 0, MAX_DISPLAY_SEARCH_RESULTS), 'is_numeric'))) . '") order by countries_name');
  $Qcountries->execute();

  $names_string = '';

  while ( $Qcountries->next() ) {
    $names_string .= HTML::hiddenField('batch[]', $Qcountries->valueInt('countries_id')) . '<b>' . $Qcountries->valueProtected('countries_name') . '</b>, ';
  }

  if ( !empty($names_string) ) {
    $names_string = substr($names_string, 0, -2);
  }
?>

<h1><?php echo $OSCOM_Template->getIcon(32) . HTML::link(OSCOM::getLink(), $OSCOM_Template->getPageTitle()); ?></h1>

<?php
  if ( $OSCOM_MessageStack->exists() ) {
    echo $OSCOM_MessageStack->get();
  }
?>

<div class="infoBox">
  <h3><?php echo HTML::icon('trash.png') . ' ' . OSCOM::getDef('action_heading_batch_delete_countries'); ?></h3>

  <form name="cDeleteBatch" class="dataForm" action="<?php echo OSCOM::getLink(null, null, 'BatchDelete&Process'); ?>" method="post">

  <p><?php echo OSCOM::getDef('introduction_batch_delete_countries'); ?></p>

  <p><?php echo $names_string; ?></p>

  <p><?php echo HTML::button(array('priority' => 'primary', 'icon' => 'trash', 'title' => OSCOM::getDef('button_delete'))) . ' ' . HTML::button(array('href' => OSCOM::getLink(), 'priority' => 'secondary', 'icon' => 'close', 'title' => OSCOM::getDef('button_cancel'))); ?></p>

  </form>
</div>
