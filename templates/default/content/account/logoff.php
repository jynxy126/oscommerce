<?php
/*
  $Id: login.php 156 2005-08-04 22:13:08 +0200 (Do, 04 Aug 2005) hpdl $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2005 osCommerce

  Released under the GNU General Public License
*/
?>

<h1><?php echo $osC_Template->getPageTitle(); ?></h1>

<div>
  <div style="float: left;"><?php echo tep_image(DIR_WS_IMAGES . 'table_background_man_on_board.gif', $osC_Template->getPageTitle()); ?></div>

  <div style="padding-top: 30px;">
    <p><?php echo TEXT_LOGOFF; ?></p>
  </div>
</div>

<div class="submitFormButtons" style="text-align: right;">
  <?php echo '<a href="' . tep_href_link(FILENAME_DEFAULT) . '">' . tep_image_button('button_continue.gif', IMAGE_BUTTON_CONTINUE) . '</a>'; ?>
</div>