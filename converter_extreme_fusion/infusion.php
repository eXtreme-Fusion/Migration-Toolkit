<?php
/*********************************************************
| eXtreme-Fusion
| Content Management System
|
| Copyright (c) 2005-2013 eXtreme-Fusion Crew
| http://extreme-fusion.org/
|
| This program is released as free software under the
| Affero GPL license. You can redistribute it and/or
| modify it under the terms of this license which you
| can read by viewing the included agpl.txt or online
| at www.gnu.org/licenses/agpl.html. Removal of this
| copyright header is strictly prohibited without
| written permission from the original author(s).
| 
**********************************************************/
/*
	Autor: Rafa³ Krupiñski
	Data utworzenia: 20 czerwca 2013
	E-mail: kontakt@rafik.eu
	Adres WWW: http://rafik.eu 
*/
if ( ! defined('IN_FUSION') || ! checkrights('I')) 
{
	header('Location: ../../index.php'); exit; 
}

if (file_exists(INFUSIONS.'converter_extreme_fusion/locale/'.$settings['locale'].'.php')) 
{
	include INFUSIONS.'converter_extreme_fusion/locale/'.$settings['locale'].'.php';
} 
else
{
	include INFUSIONS.'converter_extreme_fusion/locale/English.php';
}

$inf_title = $locale['EFC_name'];
$inf_description = $locale['EFC_desc'];
$inf_version = '1.0';
$inf_developer = 'Rafik89';
$inf_email = 'kontakt@rafik.eu';
$inf_weburl = 'http://rafik.eu';

$inf_folder = 'converter_extreme_fusion';
$inf_admin_image = '';
$inf_admin_panel = 'converter_extreme_fusion.php';
?>