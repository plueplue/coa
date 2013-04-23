<?php if(!defined('IN_GS')){ die('you cannot load this page directly.'); }
/*
Coa
Start Theme
*/

// Check Constants
if (!defined('THEMEPATH')) die('<h3>THEMEPATH was not defined.</h3>');
if (!defined('PAGETEMPLATE')) die('<h3>PAGETEMPLATE was not defined.</h3>');
if (!defined('CONSTANTTEMPLATE')) die('<h3>CONSTANTTEMPLATE was not defined.</h3>');
if (!defined('TRANSLATIONURL')) die('<h3>TRANSLATIONURL was not defined.</h3>');

// Current Page Id
if(isset($_GET['id'])) $curPid = $_GET['id']; 
else $curPid = 'index';

// Required Includes
require(GSPLUGINPATH.'CoaPlugin/inc/parse_coa.php');
require(GSPLUGINPATH.'CoaPlugin/inc/template.php');
require(GSPLUGINPATH.'CoaPlugin/inc/constant_objects.php');
require(GSPLUGINPATH.'CoaPlugin/inc/page_objects.php');
require(GSPLUGINPATH.'CoaPlugin/inc/navigation.php');
require(GSPLUGINPATH.'CoaPlugin/inc/post_process.php');

// Fire
ob_start('post_process');
makeConstant($configOA);
ob_start('replaceTags');
ob_start('replaceTags');
makePage($path = PAGETEMPLATE, $configOA, $isPlace = 0);
ob_end_flush();
ob_end_flush();
ob_end_flush();
?>