<?php
/*
  osCommerce Online Merchant $osCommerce-SIG$
  Copyright (c) 2010 osCommerce (http://www.oscommerce.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License v2 (1991)
  as published by the Free Software Foundation.
*/

  namespace osCommerce\OM\Core;

  use osCommerce\OM\Core\ErrorHandler;
  use osCommerce\OM\Core\Registry;
  use osCommerce\OM\Core\HTML;

  define('OSCOM_BASE_DIRECTORY', realpath(__DIR__ . '/../') . '/');

  class OSCOM {
    const TIMESTAMP_START = OSCOM_TIMESTAMP_START;
    const BASE_DIRECTORY = OSCOM_BASE_DIRECTORY;

    protected static $_version;
    protected static $_request_type;
    protected static $_site;
    protected static $_application;
    protected static $_config;

    public static function initialize() {
      static::loadConfig();

      ErrorHandler::initialize();

      static::setSite();

      if ( !static::siteExists(static::getSite()) ) {
        trigger_error('Site \'' . static::getSite() . '\' does not exist', E_USER_ERROR);
        exit();
      }

      static::setSiteApplication();

      call_user_func(array('osCommerce\\OM\\Core\\Site\\' . static::getSite() . '\\Controller', 'initialize'));
    }

    public static function siteExists($site) {
      return class_exists('osCommerce\\OM\\Core\\Site\\' . $site . '\\Controller');
    }

    public static function setSite($site = null) {
      if ( isset($site) ) {
        if ( !static::siteExists($site) ) {
          trigger_error('Site \'' . $site . '\' does not exist, using default \'' . static::getDefaultSite() . '\'', E_USER_ERROR);
          $site = static::getDefaultSite();
        }
      } else {
        $site = static::getDefaultSite();

        if ( !empty($_GET) ) {
          $requested_site = HTML::sanitize(basename(key(array_slice($_GET, 0, 1, true))));

          if ( static::siteExists($requested_site) ) {
            $site = $requested_site;
          }
        }
      }

      if ( !empty($site) ) {
        static::$_site = $site;
      }
    }

    public static function getSite() {
      return static::$_site;
    }

    public static function getDefaultSite() {
      return static::getConfig('default_site', 'OSCOM');
    }

    public static function siteApplicationExists($application) {
      return class_exists('osCommerce\\OM\\Core\\Site\\' . static::getSite() . '\\Application\\' . $application . '\\Controller');
    }

    public static function setSiteApplication($application = null) {
      if ( isset($application) ) {
        if ( !static::siteApplicationExists($application) ) {
          trigger_error('Application \'' . $application . '\' does not exist for Site \'' . static::getSite() . '\', using default \'' . static::getDefaultSiteApplication() . '\'', E_USER_ERROR);
          $application = null;
        }
      } else {
        if ( !empty($_GET) ) {
          $requested_application = HTML::sanitize(basename(key(array_slice($_GET, 0, 1, true))));

          if ( $requested_application == static::getSite() ) {
            $requested_application = HTML::sanitize(basename(key(array_slice($_GET, 1, 1, true))));
          }

          if ( !empty($requested_application) && static::siteApplicationExists($requested_application) ) {
            $application = $requested_application;
          }
        }
      }

      if ( empty($application) ) {
        $application = static::getDefaultSiteApplication();
      }

      static::$_application = $application;
    }

    public static function getSiteApplication() {
      return static::$_application;
    }

    public static function getDefaultSiteApplication() {
      return call_user_func(array('osCommerce\\OM\\Core\\Site\\' . static::getSite() . '\\Controller', 'getDefaultApplication'));
    }

    public static function loadConfig() {
      $ini = parse_ini_file(static::BASE_DIRECTORY . 'Config/settings.ini', true);

      if ( file_exists(static::BASE_DIRECTORY . 'Config/local_settings.ini') ) {
        $local = parse_ini_file(static::BASE_DIRECTORY . 'Config/local_settings.ini', true);

        $ini = array_merge($ini, $local);
      }

      static::$_config = $ini;
    }

    public static function getConfig($key, $group = null) {
      if ( !isset($group) ) {
        $group = static::getSite();
      }

      return static::$_config[$group][$key];
    }

    public static function configExists($key, $group = null) {
      if ( !isset($group) ) {
        $group = static::getSite();
      }

      return isset(static::$_config[$group][$key]);
    }

    public static function setConfig($key, $value, $group = null) {
      if ( !isset($group) ) {
        $group = static::getSite();
      }

      static::$_config[$group][$key] = $value;
    }

    public static function getVersion() {
      if ( !isset(static::$_version) ) {
        $v = trim(file_get_contents(static::BASE_DIRECTORY . 'version.txt'));

        if ( preg_match('/^(\d+\.)?(\d+\.)?(\d+)$/', $v) ) {
          static::$_version = $v;
        } else {
          trigger_error('Version number is not numeric. Please verify: ' . static::BASE_DIRECTORY . 'version.txt');
        }
      }

      return static::$_version;
    }

    protected static function setRequestType() {
      static::$_request_type = (isset($_SERVER['HTTPS']) && (strtolower($_SERVER['HTTPS']) == 'on') ? 'SSL' : 'NONSSL');
    }

    public static function getRequestType() {
      if ( !isset(static::$_request_type) ) {
        static::setRequestType();
      }

      return static::$_request_type;
    }

/**
 * Return an internal URL address.
 *
 * @param string $site The Site to link to. Default: The currently used Site.
 * @param string $application The Site Application to link to. Default: The currently used Site Application.
 * @param string $parameters Parameters to add to the link. Example: key1=value1&key2=value2
 * @param string $connection The type of connection to use for the link. Values: NONSSL, SSL, AUTO. Default: NONSSL.
 * @param bool $add_session_id Add the session ID to the link. Default: True.
 * @param bool $search_engine_safe Use search engine safe URLs. Default: True.
 * @return string The URL address.
 */

    public static function getLink($site = null, $application = null, $parameters = null, $connection = 'NONSSL', $add_session_id = true, $search_engine_safe = true) {
      if ( empty($site) ) {
        $site = static::getSite();
      }

      if ( empty($application) && ($site == static::getSite()) ) {
        $application = static::getSiteApplication();
      }

      if ( !in_array($connection, array('NONSSL', 'SSL', 'AUTO')) ) {
        $connection = 'NONSSL';
      }

      if ( !is_bool($add_session_id) ) {
        $add_session_id = true;
      }

      if ( !is_bool($search_engine_safe) ) {
        $search_engine_safe = true;
      }

      if ( $connection == 'AUTO' ) {
        if ( (static::getRequestType() == 'SSL') && (static::getConfig('enable_ssl', $site) == 'true') ) {
          $link = static::getConfig('https_server', $site) . static::getConfig('dir_ws_https_server', $site);
        } else {
          $link = static::getConfig('http_server', $site) . static::getConfig('dir_ws_http_server', $site);
        }
      } elseif ( ($connection == 'SSL') && (static::getConfig('enable_ssl', $site) == 'true') ) {
        $link = static::getConfig('https_server', $site) . static::getConfig('dir_ws_https_server', $site);
      } else {
        $link = static::getConfig('http_server', $site) . static::getConfig('dir_ws_http_server', $site);
      }

      $link .= static::getConfig('bootstrap_file', 'OSCOM') . '?';

      if ( $site != static::getDefaultSite() ) {
        $link .= $site . '&';
      }

      if ( !empty($application) && ($application != static::getDefaultSiteApplication()) ) {
        $link .= $application . '&';
      }

      if ( !empty($parameters) ) {
        $link .= $parameters . '&';
      }

      if ( ($add_session_id === true) && Registry::exists('Session') && Registry::get('Session')->hasStarted() && (SERVICE_SESSION_FORCE_COOKIE_USAGE == '-1') ) {
        if ( strlen(SID) > 0 ) {
          $_sid = SID;
        } elseif ( ((static::getRequestType() == 'NONSSL') && ($connection == 'SSL') && (static::getConfig('enable_ssl', $site) == 'true')) || ((static::getRequestType() == 'SSL') && ($connection != 'SSL')) ) {
          if ( static::getConfig('http_cookie_domain', $site) != static::getConfig('https_cookie_domain', $site) ) {
            $_sid = Registry::get('Session')->getName() . '=' . Registry::get('Session')->getID();
          }
        }
      }

      if ( isset($_sid) ) {
        $link .= HTML::output($_sid);
      }

      while ( (substr($link, -1) == '&') || (substr($link, -1) == '?') ) {
        $link = substr($link, 0, -1);
      }

      if ( ($search_engine_safe === true) && Registry::exists('osC_Services') && Registry::get('osC_Services')->isStarted('sefu') ) {
        $link = str_replace(array('?', '&', '='), array('/', '/', ','), $link);
      }

      return $link;
    }

/**
 * Return an internal URL address for public objects.
 *
 * @param string $url The object location from the public/sites/SITE/ directory.
 * @param string $parameters Parameters to add to the link. Example: key1=value1&key2=value2
 * @return string The URL address.
 */

    public static function getPublicSiteLink($url, $parameters = null) {
      $link = 'public/sites/' . static::getSite() . '/' . $url;

      if ( !empty($parameters) ) {
        $link .= '?' . $parameters;
      }

      while ( (substr($link, -1) == '&') || (substr($link, -1) == '?') ) {
        $link = substr($link, 0, -1);
      }

      return $link;
    }

/**
 * Return an internal URL address for an RPC call.
 *
 * @param string $site The Site to link to. Default: The currently used Site.
 * @param string $application The Site Application to link to. Default: The currently used Site Application.
 * @param string $parameters Parameters to add to the link. Example: key1=value1&key2=value2
 * @param string $connection The type of connection to use for the link. Values: NONSSL, SSL, AUTO. Default: NONSSL.
 * @param bool $add_session_id Add the session ID to the link. Default: True.
 * @param bool $search_engine_safe Use search engine safe URLs. Default: True.
 * @return string The URL address.
 */

    public static function getRPCLink($site = null, $application = null, $parameters = null, $connection = 'NONSSL', $add_session_id = true, $search_engine_safe = true) {
      if ( empty($site) ) {
        $site = static::getSite();
      }

      if ( empty($application) ) {
        $application = static::getSiteApplication();
      }

      return static::getLink('RPC', $site, $application . '&' . $parameters, $connection, $add_session_id, $search_engine_safe);
    }

    public static function redirect($url) {
      if ( (strpos($url, "\n") !== false) || (strpos($url, "\r") !== false) ) {
        $url = static::getLink(OSCOM::getDefaultSite());
      }

      if ( strpos($url, '&amp;') !== false ) {
        $url = str_replace('&amp;', '&', $url);
      }

      header('Location: ' . $url);

      exit;
    }

/**
 * Return a language definition
 *
 * @param string $key The language definition to return
 * @return string The language definition
 */

    public static function getDef($key) {
      return Registry::get('Language')->get($key);
    }

/**
 * Execute database queries
 *
 * @param string $procedure The name of the database query to execute
 * @param array $data Parameters passed to the database query
 * @param string $ns The namespace the database query is stored in
 * @return mixed The result of the database query
 */
    public static function callDB($procedure, $data = null, $type = 'Application') {
      $OSCOM_Database = Registry::get('PDO');

      $call = explode('\\', $procedure);

      switch ( $type ) {
        case 'Core':

          $procedure = array_pop($call);
          $ns = 'osCommerce\\OM\\Core';

          if ( !empty($call) ) {
            $ns .= '\\' . implode('\\', $call);
          }

          break;

        case 'Site':

          $ns = 'osCommerce\\OM\\Core\\Site\\' . $call[0];
          $procedure = $call[1];

          break;

        case 'Application':
        default:

          $ns = 'osCommerce\\OM\\Core\\Site\\' . $call[0] . '\\Application\\' . $call[1];
          $procedure = $call[2];
      }

      $db_driver = $OSCOM_Database->getDriver();

      if ( !class_exists($ns . '\\SQL\\' . $db_driver . '\\' . $procedure) ) {
        if ( $OSCOM_Database->hasDriverParent() && class_exists($ns . '\\SQL\\' . $OSCOM_Database->getDriverParent() . '\\' . $procedure) ) {
          $db_driver = $OSCOM_Database->getDriverParent();
        } else {
          $db_driver = 'SqlBuilder';
        }
      }

      return call_user_func(array($ns . '\\SQL\\' . $db_driver . '\\' . $procedure, 'execute'), $data);
    }

/**
 * Set a cookie
 *
 * @param string $name The name of the cookie
 * @param string $value The value of the cookie
 * @param int $expire Unix timestamp of when the cookie should expire
 * @param string $path The path on the server for which the cookie will be available on
 * @param string $domain The The domain that the cookie is available on
 * @param boolean $secure Indicates whether the cookie should only be sent over a secure HTTPS connection
 * @param boolean $httpOnly Indicates whether the cookie should only accessible over the HTTP protocol
 * @since v3.0.0
 */

    public static function setCookie($name, $value = null, $expires = 0, $path = null, $domain = null, $secure = false, $httpOnly = false) {
      if ( !isset($path) ) {
        $path = (static::getRequestType() == 'NONSSL') ? static::getConfig('http_cookie_path') : static::getConfig('https_cookie_path');
      }

      if ( !isset($domain) ) {
        $domain = (static::getRequestType() == 'NONSSL') ? static::getConfig('http_cookie_domain') : static::getConfig('https_cookie_domain');
      }

      header('Set-Cookie: ' . $name . '=' . urlencode($value) . '; expires=' . date('D, d-M-Y H:i:s T', $expires) . '; path=' . $path . '; domain=' . $domain . (($secure === true) ? ' secure;' : '') . (($httpOnly === true) ? ' httponly;' : ''));
    }

/**
 * Get the IP address of the client
 *
 * @since v3.0.0
 */

    public static function getIPAddress() {
      if ( isset($_SERVER['HTTP_X_FORWARDED_FOR']) ) {
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
      } elseif ( isset($_SERVER['HTTP_CLIENT_IP']) ) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
      } else {
        $ip = $_SERVER['REMOTE_ADDR'];
      }

      return $ip;
    }
  }
?>
