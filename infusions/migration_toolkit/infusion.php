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
if ( ! defined('IN_FUSION') || ! checkrights('I')) 
{
	header('Location: ../../index.php'); exit; 
}

if (file_exists(INFUSIONS.'migration_toolkit/locale/'.$settings['locale'].'.php')) 
{
	include INFUSIONS.'migration_toolkit/locale/'.$settings['locale'].'.php';
} 
else
{
	include INFUSIONS.'migration_toolkit/locale/English.php';
}

$inf_title = $locale['EFC_name'];
$inf_description = $locale['EFC_desc'];
$inf_version = '1.0';
$inf_developer = 'eXtreme Crew';
$inf_email = 'rafik89@extreme-fusion.pl';
$inf_weburl = 'http://extreme-fusion.org';

$inf_folder = 'migration_toolkit';
$inf_admin_image = '';
$inf_admin_panel = 'migration_toolkit.php';