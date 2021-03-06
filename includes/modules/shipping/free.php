<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2009 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  class osC_Shipping_free extends osC_Shipping {
    protected $icon;
    protected $_title;
    protected $_code = 'free';
    protected $_status = false;
    protected $_sort_order;

    public function __construct() {
      $this->icon = '';

      $this->_title = __('shipping_free_title');
      $this->_description = __('shipping_free_description');
      $this->_status = (defined('MODULE_SHIPPING_FREE_STATUS') && (MODULE_SHIPPING_FREE_STATUS == 'True') ? true : false);
    }

    public function initialize() {
      global $osC_Database, $osC_ShoppingCart;

      if ($osC_ShoppingCart->getTotal() >= MODULE_SHIPPING_FREE_MINIMUM_ORDER) {
        if ($this->_status === true) {
          if ((int)MODULE_SHIPPING_FREE_ZONE > 0) {
            $check_flag = false;

            $Qcheck = $osC_Database->query('select zone_id from :table_zones_to_geo_zones where geo_zone_id = :geo_zone_id and (zone_country_id = :zone_country_id or zone_country_id = 0) order by zone_id');
            $Qcheck->bindTable(':table_zones_to_geo_zones', TABLE_ZONES_TO_GEO_ZONES);
            $Qcheck->bindInt(':geo_zone_id', MODULE_SHIPPING_FREE_ZONE);
            $Qcheck->bindInt(':zone_country_id', $osC_ShoppingCart->getShippingAddress('country_id'));
            $Qcheck->execute();

            while ($Qcheck->next()) {
              if ($Qcheck->valueInt('zone_id') < 1) {
                $check_flag = true;
                break;
              } elseif ($Qcheck->valueInt('zone_id') == $osC_ShoppingCart->getShippingAddress('zone_id')) {
                $check_flag = true;
                break;
              }
            }

            $this->_status = $check_flag;
          } else {
            $this->_status = true;
          }
        }
      } else {
        $this->_status = false;
      }
    }

    public function quote() {
      global $osC_Currencies;

      $this->quotes = array('id' => $this->_code,
                            'module' => $this->_title,
                            'methods' => array(array('id' => $this->_code,
                                                     'title' => sprintf(__('shipping_free_for_amount'), $osC_Currencies->format(MODULE_SHIPPING_FREE_MINIMUM_ORDER)),
                                                     'cost' => 0)),
                            'tax_class_id' => 0);

      if (!empty($this->icon)) $this->quotes['icon'] = osc_image($this->icon, $this->_title);

      return $this->quotes;
    }
  }
?>
