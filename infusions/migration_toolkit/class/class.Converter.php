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

class Converter
{
	// Przechowuje tablicę z tłumaczeniem na wybrane języki
	protected $_lang 			= NULL;
	
	// Przechowuje prefix tabel systemu eXtreme Fusion
	protected $_db_prefix 		= NULL;
	
	// Przechowuje adres hosta bazy danych systemu eXtreme Fusion
	protected $_db_host 		= NULL;
	
	// Przechowuje nazwę użytkownika bazy danych systemu eXtreme Fusion
	protected $_db_user 		= NULL;
	
	// Przechowuje hasło użytkownika bazy danych systemu eXtreme Fusion
	protected $_db_pass 		= NULL;
	
	// Przechowuje nazwę tabeli systemu eXtreme Fusion
	protected $_db_name 		= NULL;
	
	// Przechowuje nazwę tabeli systemu eXtreme Fusion
	protected $_db_port 		= 3306;
	
	// Przechowuje metode kodowania znaków
	protected $_charset 		= 'utf8';
	
	// Przechowuje przechowuje sposób porównywania znaków
	protected $_collate 		= 'utf8_general_ci';
	
	// Przechowuje numer wersji systemu do którego zostanie zaktualizowany system
	protected $_new_ef_version 	= '5.0.3';
	
	// Przechowuje numer wersji systemu eXtreme-Fusion z którego powinno się dokonać aktualizacji systemu
	protected $_ef_version 		= '4.17';

	/*
		Konstruktor klasy Converter
	*/
	public function __construct($lang, array $db)
	{
		$this->_lang		= $lang;
		$this->_db_prefix	= $db['0'];
		$this->_db_host		= $db['1'];
		$this->_db_user		= $db['2'];
		$this->_db_pass		= $db['3'];
		$this->_db_name		= $db['4'];
		
		// Nawiązanie połączenia z bazą danych.
		$this->dbconnect();

	}

	/*
		Metoda wyświetlająca status wykonanych operacji
	*/
	public function showStatus($var)
	{
		return $this->status($var) ? $this->_lang['EFC_status_ok'] : $this->_lang['EFC_status_error'];
	}

	/*
		Metoda odpowiedzialna za kolejne kroki operacji
		Operacja jest podzielona na kilka kroków
	*/
	public function stepNum($num) 
	{
		$data = array();
		if($num === 1)
		{
			// Tworzenie nowych tabel lub usuwanie starych i tworzenie nowej struktury
			$this->createAdmin() ? 					$data[] = array('name' => 'admin',					'status' => TRUE) : $data[] = array('name' => 'admin', 					'status' => FALSE);
			$this->createBBcode() ? 				$data[] = array('name' => 'bbcode',					'status' => TRUE) : $data[] = array('name' => 'bbcode', 				'status' => FALSE);
			$this->createUserFields() ? 			$data[] = array('name' => 'user_fields',			'status' => TRUE) : $data[] = array('name' => 'user_fields', 			'status' => FALSE);
			$this->createUserFieldCats() ? 			$data[] = array('name' => 'user_field_cats',		'status' => TRUE) : $data[] = array('name' => 'user_field_cats',		'status' => FALSE);
			$this->createUsersOnline() ? 			$data[] = array('name' => 'users_online',			'status' => TRUE) : $data[] = array('name' => 'users_online',			'status' => FALSE);
			$this->createUsersData() ? 				$data[] = array('name' => 'users_data',				'status' => TRUE) : $data[] = array('name' => 'users_data',				'status' => FALSE);
			$this->createNavigation() ? 			$data[] = array('name' => 'navigation',				'status' => TRUE) : $data[] = array('name' => 'navigation',				'status' => FALSE);
			$this->createGroups() ? 				$data[] = array('name' => 'groups',					'status' => TRUE) : $data[] = array('name' => 'groups',					'status' => FALSE);
			$this->createPermissionsSections() ?	$data[] = array('name' => 'permissions_sections',	'status' => TRUE) : $data[] = array('name' => 'permissions_sections',	'status' => FALSE);
			$this->createPermissions() ?			$data[] = array('name' => 'permissions',			'status' => TRUE) : $data[] = array('name' => 'permissions',			'status' => FALSE);
			$this->createNotes() ?					$data[] = array('name' => 'notes',					'status' => TRUE) : $data[] = array('name' => 'notes',					'status' => FALSE);
			$this->createLogs() ?					$data[] = array('name' => 'logs',					'status' => TRUE) : $data[] = array('name' => 'logs',					'status' => FALSE);
			$this->createLinks() ?					$data[] = array('name' => 'links',					'status' => TRUE) : $data[] = array('name' => 'links',					'status' => FALSE);
			$this->createModules() ?				$data[] = array('name' => 'modules',				'status' => TRUE) : $data[] = array('name' => 'modules',				'status' => FALSE);
			$this->createTags() ?					$data[] = array('name' => 'tags',					'status' => TRUE) : $data[] = array('name' => 'tags',					'status' => FALSE);
			$this->createPages() ?					$data[] = array('name' => 'pages',					'status' => TRUE) : $data[] = array('name' => 'pages',					'status' => FALSE);
			$this->createPagesTypes() ?				$data[] = array('name' => 'pages_types',			'status' => TRUE) : $data[] = array('name' => 'pages_types',			'status' => FALSE);
			$this->createPagesCategories() ?		$data[] = array('name' => 'pages_categories',		'status' => TRUE) : $data[] = array('name' => 'pages_categories',		'status' => FALSE);
			$this->createPagesCustomSettings() ?	$data[] = array('name' => 'pages_custom_settings',	'status' => TRUE) : $data[] = array('name' => 'pages_custom_settings',	'status' => FALSE);
			$this->createTimeFormats() ?			$data[] = array('name' => 'time_formats',			'status' => TRUE) : $data[] = array('name' => 'time_formats',			'status' => FALSE);
		}
		elseif($num === 2)
		{
			// Tworzenie ustawień
			$this->createSettings()  ? 				$data[] = array('name' => 'settings',				'status' => TRUE) : $data[] = array('name' => 'settings', 				'status' => FALSE);
		}
		elseif($num === 3)
		{
			// Usuwanie nie potrzebnych tabel w nowym systemie
			$this->dropOldTables() ? 				$data[] = array('name' => 'drop_old_tables',		'status' => TRUE) : $data[] = array('name' => 'drop_old_tables', 		'status' => FALSE);
		}
		elseif($num === 4)
		{
			// Przetwarzanie istniejących tabel aby były zgodne z nowym systeme a dane nie zostały usunięte.
			$this->changeBlacklistFields() ? 		$data[] = array('name' => 'blacklist',				'status' => TRUE) : $data[] = array('name' => 'blacklist', 				'status' => FALSE);
			$this->changeCommentsFields() ? 		$data[] = array('name' => 'comments',				'status' => TRUE) : $data[] = array('name' => 'comments', 				'status' => FALSE);
			$this->changeMessagesFields() ? 		$data[] = array('name' => 'messages',				'status' => TRUE) : $data[] = array('name' => 'messages', 				'status' => FALSE);
			$this->changeNewsFields() ? 			$data[] = array('name' => 'news',					'status' => TRUE) : $data[] = array('name' => 'news', 					'status' => FALSE);
			$this->changeNewsCatsFields() ? 		$data[] = array('name' => 'news_cats',				'status' => TRUE) : $data[] = array('name' => 'news_cats', 				'status' => FALSE);
			$this->changeOnlineFields() ? 			$data[] = array('name' => 'online',					'status' => TRUE) : $data[] = array('name' => 'online', 				'status' => FALSE);
			$this->changePanelsFields() ? 			$data[] = array('name' => 'panels',					'status' => TRUE) : $data[] = array('name' => 'panels', 				'status' => FALSE);
			$this->changeSmileysFields() ? 			$data[] = array('name' => 'smileys',				'status' => TRUE) : $data[] = array('name' => 'smileys', 				'status' => FALSE);
		
		}
		elseif($num === 5)
		{
			// Przetwarzanie tabeli użytkowników
			$this->changeUsersFields() ? 			$data[] = array('name' => 'users',					'status' => TRUE) : $data[] = array('name' => 'users', 					'status' => FALSE);
		}
		elseif($num === 6)
		{
	
		}
		elseif($num === 7)
		{

		}
		elseif($num === 8)
		{

		}
		elseif($num === 9)
		{
			$site_url = explode('infusions', $_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF']);
			
			$data = "<div style='width:800px; margin:20px auto;'>\n";
			$data .= "<div class='code'>";
			$data .= '&lt;?php<br />';
			$data .= '/*********************************************************<br />';
			$data .= '| eXtreme-Fusion 5<br />';
			$data .= '| Content Management System<br />';
			$data .= '|<br />';
			$data .= '| Copyright (c) 2005-'.date('Y').' eXtreme-Fusion Crew<br />';
			$data .= '| http://extreme-fusion.org/<br />';
			$data .= '|<br />';
			$data .= '| This program is released as free software under the<br />';
			$data .= '| Affero GPL license. You can redistribute it and/or<br />';
			$data .= '| modify it under the terms of this license which you<br />';
			$data .= '| can read by viewing the included agpl.txt or online<br />';
			$data .= '| at www.gnu.org/licenses/agpl.html. Removal of this<br />';
			$data .= '| copyright header is strictly prohibited without<br />';
			$data .= '| written permission from the original author(s).<br />';
			$data .= '| <br />';
			$data .= '**********************************************************/<br /><br />';
			$data .= 'defined(\'DS\') || define(\'DS\', DIRECTORY_SEPARATOR);<br /><br />';

			$data .= '#Database<br /><br />';

			$data .= '$_dbconfig = array(<br />';
			$data .= '    \'host\' => \''.$this->_db_host.'\',<br />';
			$data .= '    \'port\' => \''.$this->_db_port.'\',<br />';
			$data .= '    \'user\' => \''.$this->_db_user.'\',<br />';
			$data .= '    \'password\' => \''.$this->_db_pass.'\',<br />';
			$data .= '    \'database\' => \''.$this->_db_name.'\',<br />';
			$data .= '    \'prefix\' => \''.$this->_db_prefix.'\',<br />';
			$data .= '    \'charset\' => \'utf8\',<br />';
			$data .= '    \'version\' => \'eXtreme-Fusion CMS - Ninja Edition '.$this->getNeweXtremeFusionVersion().'\'<br />';
			$data .= ');<br /><br />';

			$data .= '#Routing<br /><br />';


			$data .= '$_route = array(<br />';
			$data .= '	//Change this to TRUE if your server has been configured to work with $_SERVER[\'PATH_INFO\']<br />';
			$data .= '    \'custom_furl\' => \'FALSE\',<br />';
			$data .= '	//Change this to TRUE if your server has got configured modRewrite<br />';
			$data .= '    \'custom_rewrite\' => \'FALSE\',<br />';
			$data .= ');<br /><br />';

			$data .= '#Cookie && cache<br /><br />';

			$data .= 'defined(\'COOKIE_PREFIX\') || define(\'COOKIE_PREFIX\', \'extreme_'.substr(md5(uniqid('ef5_cookie', FALSE)), 13, 7).'_\');<br />';
			$data .= 'defined(\'CACHE_PREFIX\') || define(\'CACHE_PREFIX\', \'extreme_'.substr(md5(uniqid('ef5_cache', FALSE)), 13, 7).'_\');<br /><br />';

			$data .= '#Main path && site address<br /><br />';

			$data .= 'defined(\'DIR_SITE\') || define(\'DIR_SITE\', dirname(__FILE__).DS);<br />';
			$data .= 'defined(\'ADDR_SITE\') || define(\'ADDR_SITE\', \'http://'.$site_url[0].'\');<br /><br />';

			$data .= '#Encryption<br /><br />';

			$data .= 'defined(\'CRYPT_KEY\') || define(\'CRYPT_KEY\', \''.md5(uniqid(time())).'\');<br />';
			$data .= 'defined(\'CRYPT_IV\') || define(\'CRYPT_IV\', \''.substr(md5(uniqid(time())), 4, 8).'\');';
			$data .= "</div></div>";
		}
		
		return $data;
	}
	
	/*
		Metoda prywatna, tworzy struktórę tabeli dla tabeli	admin
	*/
	private function createAdmin() 
	{
		$this->dbQuery("DROP TABLE IF EXISTS `".$this->_db_prefix."admin`");
		$query = $this->dbQuery("CREATE TABLE `".$this->_db_prefix."admin` (
			`id` MEDIUMINT UNSIGNED NOT NULL AUTO_INCREMENT,
			`permissions` VARCHAR(127) NOT NULL DEFAULT '',
			`image` VARCHAR(120) NOT NULL DEFAULT '',
			`title` VARCHAR(50) NOT NULL DEFAULT '',
			`link` VARCHAR(100) NOT NULL DEFAULT 'reserved',
			`page` TINYINT UNSIGNED NOT NULL DEFAULT '1',
			PRIMARY KEY (`id`)
		) ENGINE = InnoDB CHARACTER SET ".$this->_charset." COLLATE ".$this->_collate.";");

		if ($query)
		{
			$this->dbQuery("INSERT INTO `".$this->_db_prefix."admin` (`permissions`, `image`, `title`, `link`, `page`) VALUES
				('admin.bbcodes', 'bbcodes.png', 'BBCodes', 'bbcodes.php', 3),
				('admin.blacklist', 'blacklist.png', 'Blacklist', 'blacklist.php', 2),
				('admin.comments', 'comments.png', 'Comments', 'comments.php', 2),
				('admin.groups', 'groups.png', 'Groups', 'groups.php', 2),
				('admin.pages', 'pages.png', 'Content Pages', 'pages.php', 1),
				('admin.logs', 'logs.png', 'Logs', 'logs.php', 2),
				('admin.urls', 'urls.png', 'URLs Generator', 'urls.php', 3),
				('admin.news', 'news.png', 'News', 'news.php', 1),
				('admin.panels', 'panels.png', 'Panels', 'panels.php', 3),
				('admin.permissions', 'permissions.png', 'Permissions', 'permissions.php', 2),
				('admin.phpinfo', 'phpinfo.png', 'PHP Info', 'phpinfo.php', 3),
				('admin.security', 'security.png', 'Security Politics', 'settings_security.php', '4'),
				('admin.settings', 'settings.png', 'General', 'settings_general.php', 4),
				('admin.settings_banners', 'settings_banners.png', 'Banners', 'settings_banners.php', 4),
				('admin.settings_cache', 'settings_cache.png', 'Cache', 'settings_cache.php', 4),
				('admin.settings_time', 'settings_time.png', 'Time and Date', 'settings_time.php', 4),
				('admin.settings_registration', 'registration.png', 'Registration', 'settings_registration.php', 4),
				('admin.settings_misc', 'settings_misc.png', 'Miscellaneous', 'settings_misc.php', 4),
				('admin.settings_users', 'settings_users.png', 'User Management', 'settings_users.php', 4),
				('admin.settings_ipp', 'settings_ipp.png', 'Item per Page', 'settings_ipp.php', 4),
				('admin.settings_logs', 'logs.png', 'Logs', 'settings_logs.php', 4),
				('admin.settings_login', 'login.png', 'Login', 'settings_login.php', 4),
				('admin.settings_routing', 'router.png', 'Router', 'settings_routing.php', 4),
				('admin.navigations', 'navigations.png', 'Site Links', 'navigations.php', 3),
				('admin.smileys', 'smileys.png', 'Smileys', 'smileys.php', 3),
				('admin.user_fields', 'user_fields.png', 'User Fields', 'user_fields.php', 2),
				('admin.user_fields_cats', 'user_fields_cats.png', 'User Field Categories', 'user_field_cats.php', 2),
				('admin.users', 'users.png', 'Users', 'users.php', 2)
			");

		}
		return $query;
	}
	
	/*
		Metoda prywatna, tworzy struktórę tabeli dla tabeli
		bbcodes, oraz uzupełnia ją podstawowymi danymi
	*/
	private function createBBcode() 
	{
		$query = $this->dbQuery("CREATE TABLE `".$this->_db_prefix."bbcodes` (
			`id` MEDIUMINT UNSIGNED NOT NULL AUTO_INCREMENT,
			`name` VARCHAR(20) NOT NULL DEFAULT '',
			`order` SMALLINT UNSIGNED NOT NULL,
			PRIMARY KEY (`id`),
			KEY `order` (`order`)
		) ENGINE = InnoDB CHARACTER SET ".$this->_charset." COLLATE ".$this->_collate.";");
		
		if ($query)
		{
			$query = $this->dbQuery("INSERT INTO `".$this->_db_prefix."bbcodes` (`name`, `order`) VALUES 
				('b', '1'),
				('i', '2'),
				('u', '3'),
				('url', '4'),
				('mail', '5'),
				('img', '6'),
				('center', '7'),
				('small', '8'),
				('code', '9'),
				('quote', '10')
			");
		}
		
		return $query;
	}
	
	/*
		Metoda prywatna, tworzy struktórę tabeli dla nie istniejącej tabeli
		user_fields, oraz uzupełnia ją podstawowymi danymi
	*/
	private function createUserFields() 
	{
		$query = $this->dbQuery("CREATE TABLE `".$this->_db_prefix."user_fields` (
			`id` MEDIUMINT UNSIGNED NOT NULL AUTO_INCREMENT,
			`name` VARCHAR(50) NOT NULL,
			`index` VARCHAR(50) NOT NULL,
			`cat` MEDIUMINT UNSIGNED NOT NULL DEFAULT '1',
			`type` SMALLINT NOT NULL DEFAULT '0',
			`option` TEXT NOT NULL,
			`register` TINYINT NOT NULL DEFAULT '0',
			`hide` TINYINT NOT NULL DEFAULT '0',
			`edit` TINYINT NOT NULL DEFAULT '0',
			PRIMARY KEY (`id`)
		) ENGINE = InnoDB CHARACTER SET ".$this->_charset." COLLATE ".$this->_collate.";");
		
		if ($query)
		{
			$query = $this->dbQuery("INSERT INTO `".$this->_db_prefix."user_fields` (`name`, `index`, `cat`, `type`, `option`) VALUES 
				('".$this->_lang['First name']."', 'name', 1, 1, ''),
				('".$this->_lang['Date of birth']."', 'old', 1, 1, ''),
				('".$this->_lang['Gadu-Gadu']."', 'gg', 2, 1, ''),
				('".$this->_lang['Skype']."', 'skype', 2, 1, ''),
				('".$this->_lang['Website']."', 'www', 2, 1, ''),
				('".$this->_lang['Living place']."', 'location', 2, 1, ''),
				('".$this->_lang['Signature']."', 'sig', 3, 2, '')
			");
		}
		
		return $query;
	}
	
	/*
		Metoda prywatna, tworzy struktórę tabeli dla user_field_cats, oraz uzupełnia ją podstawowymi danymi
	*/
	private function createUserFieldCats() 
	{
		$query = $this->dbQuery("CREATE TABLE `".$this->_db_prefix."user_field_cats` (
			`id` MEDIUMINT UNSIGNED NOT NULL AUTO_INCREMENT,
			`name` VARCHAR(200) NOT NULL,
			`order` SMALLINT UNSIGNED NOT NULL,
			PRIMARY KEY (`id`)
		) ENGINE = InnoDB CHARACTER SET ".$this->_charset." COLLATE ".$this->_collate.";");
		
		if ($query)
		{
			$query = $this->dbQuery("INSERT INTO `".$this->_db_prefix."user_field_cats` (`name`, `order`) VALUES 
				('".$this->_lang['Information']."', 1),
				('".$this->_lang['Contact Information']."', 2),
				('".$this->_lang['Miscellaneous']."', 3)
			");
		}

		return $query;
	}
	
	/*
		Metoda prywatna, tworzy struktórę tabeli links
	*/
	private function createLinks() 
	{
		$query = $this->dbQuery("CREATE TABLE `".$this->_db_prefix."links` (
			`id` int(7) unsigned NOT NULL AUTO_INCREMENT,
			`link` VARCHAR(200) NOT NULL DEFAULT '',
			`file` VARCHAR(100) NOT NULL DEFAULT '',
			`full_path` VARCHAR(200) NOT NULL DEFAULT '',
			`short_path` VARCHAR(100) NOT NULL DEFAULT '',
			`datestamp` INT UNSIGNED NOT NULL DEFAULT '0',
			PRIMARY KEY (`id`),
			KEY `datestamp` (`datestamp`)
		) ENGINE = InnoDB CHARACTER SET ".$this->_charset." COLLATE ".$this->_collate.";");
		
		return $query;
	}

	/*
		Metoda prywatna, tworzy struktórę tabeli logs
	*/
	private function createLogs() 
	{
		$query = $this->dbQuery("CREATE TABLE `".$this->_db_prefix."logs` (
			`id` int(7) unsigned NOT NULL AUTO_INCREMENT,
			`action` text NOT NULL,
			`datestamp` int(10) unsigned NOT NULL,
			PRIMARY KEY (`id`)
		) ENGINE = InnoDB CHARACTER SET ".$this->_charset." COLLATE ".$this->_collate.";");
		
		return $query;
	}

	/*
		Metoda prywatna, tworzy struktórę tabeli logs
	*/
	private function createNotes() 
	{
		$query = $this->dbQuery("CREATE TABLE `".$this->_db_prefix."notes` (
			`id` SMALLINT(4) UNSIGNED NOT NULL AUTO_INCREMENT,
			`title` VARCHAR(64) NOT NULL,
			`note` TEXT NOT NULL,
			`author` MEDIUMINT UNSIGNED NOT NULL,
			`block` SMALLINT UNSIGNED NOT NULL DEFAULT '0',
			`datestamp` INT UNSIGNED NOT NULL DEFAULT '0',
			PRIMARY KEY (`id`)
		) ENGINE = InnoDB CHARACTER SET ".$this->_charset." COLLATE ".$this->_collate.";");
		
		return $query;
	}

	/*
		Metoda prywatna, tworzy struktórę tabeli permissions
	*/
	private function createPermissions() 
	{
		$query_1 = $this->dbQuery("CREATE TABLE `".$this->_db_prefix."permissions` (
			`id` int(11) NOT NULL AUTO_INCREMENT,
			`name` varchar(127) CHARACTER SET latin1 NOT NULL,
			`section` int(11) NOT NULL,
			`description` varchar(255) NOT NULL,
			`is_system` tinyint(1) NOT NULL DEFAULT '0',
			PRIMARY KEY (`id`),
			UNIQUE KEY `name` (`name`)
		) ENGINE = InnoDB CHARACTER SET ".$this->_charset." COLLATE ".$this->_collate.";");
		
		$query_2 = $this->dbQuery("INSERT INTO `".$this->_db_prefix."permissions` (`name`, `section`, `description`, `is_system`) VALUES
			('admin.login', 1, '".$this->_lang['Perm: admin login']."', 1),
			('site.login', 2, '".$this->_lang['Perm: user login']."', 1),
			('site.comment.add', 2, '".$this->_lang['Perm: comment']."', 1),
			('site.comment.edit', 2, '".$this->_lang['Perm: comment edit']."', 1),
			('site.comment.edit.all', 2, '".$this->_lang['Perm: comment edit all']."', 1),
			('site.comment.delete', 2, '".$this->_lang['Perm: comment delete']."', 1),
			('site.comment.delete.all', 2, '".$this->_lang['Perm: comment delete all']."', 1),
			('admin.bbcodes', 1, '".$this->_lang['Perm: admin bbcodes']."', 1),
			('admin.blacklist', 1, '".$this->_lang['Perm: admin blacklist']."', 1),
			('admin.comments', 1, '".$this->_lang['Perm: admin comments']."', 1),
			('admin.logs', 1, '".$this->_lang['Perm: admin logs']."', 1),
			('admin.urls', 1, '".$this->_lang['Perm: admin urls']."', 1),
			('admin.news_cats', 1, '".$this->_lang['Perm: admin news_cats']."', 1),
			('admin.news', 1, '".$this->_lang['Perm: admin news']."', 1),
			('admin.panels', 1, '".$this->_lang['Perm: admin panels']."', 1),
			('admin.permissions', 1, '".$this->_lang['Perm: admin permissions']."', 1),
			('admin.phpinfo', 1, '".$this->_lang['Perm: admin phpinfo']."', 1),
			('admin.groups', 1, '".$this->_lang['Perm: admin groups']."', 1),
			('admin.security', 1, '".$this->_lang['Perm: admin security']."', 1),
			('admin.settings', 1, '".$this->_lang['Perm: admin settings']."', 1),
			('admin.settings_banners', 1, '".$this->_lang['Perm: admin settings_banners']."', 1),
			('admin.settings_cache', 1, '".$this->_lang['Perm: admin settings_cache']."', 1),
			('admin.settings_time', 1, '".$this->_lang['Perm: admin settings_time']."', 1),
			('admin.settings_registration', 1, '".$this->_lang['Perm: admin settings_registration']."', 1),
			('admin.settings_misc', 1, '".$this->_lang['Perm: admin settings_misc']."', 1),
			('admin.settings_users', 1, '".$this->_lang['Perm: admin settings_users']."', 1),
			('admin.settings_ipp', 1, '".$this->_lang['Perm: admin settings_ipp']."', 1),
			('admin.settings_logs', 1, '".$this->_lang['Perm: admin settings_logs']."', 1),
			('admin.navigations', 1, '".$this->_lang['Perm: admin navigations']."', 1),
			('admin.smileys', 1, '".$this->_lang['Perm: admin smileys']."', 1),
			('admin.upgrade', 1, '".$this->_lang['Perm: admin upgrade']."', 1),
			('admin.user_fields', 1, '".$this->_lang['Perm: admin user_fields']."', 1),
			('admin.user_fields_cats', 1, '".$this->_lang['Perm: admin user_fields_cats']."', 1),
			('admin.users', 1, '".$this->_lang['Perm: admin users']."', 1)
		");

		return ($query_1 && $query_2 ? TRUE : FALSE);
	}

	/*
		Metoda prywatna, tworzy struktórę tabeli permissions_sections
	*/
	private function createPermissionsSections() 
	{
		$query_1 = $this->dbQuery("CREATE TABLE `".$this->_db_prefix."permissions_sections` (
			`id` int(11) NOT NULL AUTO_INCREMENT,
			`name` varchar(128) NOT NULL,
			`description` varchar(255) NOT NULL,
			`is_system` tinyint(1) NOT NULL DEFAULT '0',
			PRIMARY KEY (`id`)
		) ENGINE = InnoDB CHARACTER SET ".$this->_charset." COLLATE ".$this->_collate.";");
		
		$query_2 = $this->dbQuery("INSERT INTO `".$this->_db_prefix."permissions_sections` (`name`, `description`, `is_system`) VALUES ('admin', '".$this->_lang['Administration']."', 1), ('site', '".$this->_lang['Site']."', 1)");

		return ($query_1 && $query_2 ? TRUE : FALSE);
	}

	/*
		Metoda prywatna, tworzy struktórę tabeli groups
	*/
	private function createGroups() 
	{
		$query_1 = $this->dbQuery("CREATE TABLE `".$this->_db_prefix."groups` (
			`id` int(11) NOT NULL AUTO_INCREMENT,
			`title` varchar(127) NOT NULL,
			`description` varchar(255) DEFAULT NULL,
			`format` varchar(255) NOT NULL DEFAULT '{username}',
			`permissions` text NOT NULL,
			`team` tinyint(1) NOT NULL DEFAULT '0',
			PRIMARY KEY (`id`)
		) ENGINE = InnoDB CHARACTER SET ".$this->_charset." COLLATE ".$this->_collate.";");
		
		$query_2 = $this->dbQuery("INSERT INTO `".$this->_db_prefix."groups` (`title`, `description`, `format`, `permissions`) VALUES ('".$this->_lang['Admin']."', '".$this->_lang['Group: admin']."', '<span style=\"color:#99bb00\">{username}</span>', '".serialize(array('*'))."'), ('".$this->_lang['User']."', '".$this->_lang['Group: user']."', '{username}', '".serialize(array('site.login', 'site.comment', 'site.comment.add', 'site.comment.edit'))."'), ('".$this->_lang['Guest']."', '".$this->_lang['Group: guest']."', '{username}', '".serialize(array())."')");

		return ($query_1 && $query_2 ? TRUE : FALSE);
	}

	/*
		Metoda prywatna, tworzy struktórę tabeli navigation
	*/
	private function createNavigation() 
	{
		$query_1 = $this->dbQuery("CREATE TABLE `".$this->_db_prefix."navigation` (
			`id` MEDIUMINT UNSIGNED NOT NULL AUTO_INCREMENT,
			`name` VARCHAR(100) NOT NULL DEFAULT '',
			`url` VARCHAR(200) NOT NULL DEFAULT '',
			`visibility` VARCHAR(255) NOT NULL DEFAULT '',
			`position` TINYINT UNSIGNED NOT NULL DEFAULT '1',
			`window` TINYINT UNSIGNED NOT NULL DEFAULT '0',
			`order` SMALLINT(2) UNSIGNED NOT NULL DEFAULT '0',
			`rewrite` TINYINT UNSIGNED NOT NULL DEFAULT '1',
			PRIMARY KEY (`id`)
		) ENGINE = InnoDB CHARACTER SET ".$this->_charset." COLLATE ".$this->_collate.";");
		
		$query_2 = $this->dbQuery("INSERT INTO `".$this->_db_prefix."navigation` (`name`, `url`, `visibility`, `position`, `window`, `order`) VALUES 
			('".$this->_lang['Home']."', '', '3', '3', '0', '1'),
			('".$this->_lang['News Cats']."', 'news_cats.html', '3', '3', '0', '2'),
			('".$this->_lang['Users']."', 'users.html', '3', '3', '0', '3'),
			('".$this->_lang['Team']."', 'team.html', '3', '2', '0', '3'),
			('".$this->_lang['Rules']."', 'rules.html', '3', '2', '0', '4'),
			('".$this->_lang['Tags']."', 'tags.html', '3', '1', '0', '4'),
			('".$this->_lang['Pages']."', 'pages.html', '3', '3', '0', '5'),
			('".$this->_lang['Search']."', 'search.html', '3', '3', '0', '6')
		");

		return ($query_1 && $query_2 ? TRUE : FALSE);
	}

	/*
		Metoda prywatna, tworzy struktórę tabeli users_data
	*/
	private function createUsersData() 
	{
		$query = $this->dbQuery("CREATE TABLE `".$this->_db_prefix."users_data` (
			`user_id` int(11) AUTO_INCREMENT NOT NULL,
			`name` VARCHAR(200) NOT NULL DEFAULT '',
			`old` VARCHAR(200) NOT NULL DEFAULT '',
			`gg` VARCHAR(200) NOT NULL DEFAULT '',
			`skype` VARCHAR(200) NOT NULL DEFAULT '',
			`www` VARCHAR(200) NOT NULL DEFAULT '',
			`location` VARCHAR(200) NOT NULL DEFAULT '',
			`sig` TEXT NOT NULL,
			PRIMARY KEY (`user_id`)
		) ENGINE = InnoDB CHARACTER SET ".$this->_charset." COLLATE ".$this->_collate.";");
		
		return $query;
	}

	/*
		Metoda prywatna, tworzy struktórę tabeli users_online
	*/
	private function createUsersOnline() 
	{
		$query = $this->dbQuery("CREATE TABLE `".$this->_db_prefix."users_online` (
			`user_id` MEDIUMINT UNSIGNED NOT NULL DEFAULT '0',
			`ip` VARCHAR(20) NOT NULL DEFAULT '0.0.0.0',
			`last_activity`INT UNSIGNED NOT NULL DEFAULT '0',
			UNIQUE `user` (`user_id`, `ip`)
		) ENGINE = InnoDB CHARACTER SET ".$this->_charset." COLLATE ".$this->_collate.";");
		
		return $query;
	}

	/*
		Metoda prywatna, tworzy struktórę tabeli modules
	*/
	private function createModules() 
	{
		$query = $this->dbQuery("CREATE TABLE `".$this->_db_prefix."modules` (
			`id` MEDIUMINT UNSIGNED NOT NULL AUTO_INCREMENT,
			`title` VARCHAR(100) NOT NULL DEFAULT '',
			`folder` VARCHAR(100) NOT NULL DEFAULT '',
			`category` VARCHAR(100) NOT NULL DEFAULT '',
			`version` VARCHAR(10) NOT NULL DEFAULT '0',
			PRIMARY KEY (`id`)
		) ENGINE = InnoDB CHARACTER SET ".$this->_charset." COLLATE ".$this->_collate.";");
		
		return $query;
	}

	/*
		Metoda prywatna, tworzy struktórę tabeli tags
	*/
	private function createTags() 
	{
		$query = $this->dbQuery("CREATE TABLE `".$this->_db_prefix."tags` (
			`id` MEDIUMINT UNSIGNED NOT NULL AUTO_INCREMENT,
			`supplement` VARCHAR(100) NOT NULL DEFAULT '',
			`supplement_id` MEDIUMINT UNSIGNED NOT NULL,
			`value` TEXT NOT NULL,
			`value_for_link` TEXT NOT NULL,
			`access` VARCHAR(255) NOT NULL DEFAULT '',
			PRIMARY KEY (`id`),
			INDEX USING BTREE (supplement),
			INDEX USING BTREE (supplement_id)
		) ENGINE = InnoDB CHARACTER SET ".$this->_charset." COLLATE ".$this->_collate.";");
		
		return $query;
	}

	/*
		Metoda prywatna, tworzy struktórę tabeli pages
	*/
	private function createPages() 
	{
		$query = $this->dbQuery("CREATE TABLE `".$this->_db_prefix."pages` (
			`id` SMALLINT UNSIGNED AUTO_INCREMENT,
			`title` VARCHAR(255) NOT NULL DEFAULT '',
			`content` TEXT NOT NULL,
			`preview` MEDIUMTEXT NOT NULL,
			`description` VARCHAR(255) NOT NULL DEFAULT '',
			`type` SMALLINT UNSIGNED NOT NULL DEFAULT '0',
			`categories` VARCHAR(255) NOT NULL DEFAULT '',
			`author` SMALLINT UNSIGNED NOT NULL DEFAULT '0',
			`date` INT UNSIGNED NOT NULL DEFAULT '0',
			`url` VARCHAR(255) NOT NULL DEFAULT '',
			`thumbnail` VARCHAR(255) NOT NULL DEFAULT '',
			`settings` TINYINT UNSIGNED NOT NULL DEFAULT '0',
			PRIMARY KEY (`id`),
			INDEX USING BTREE (type),
			INDEX USING BTREE (categories),
			INDEX USING BTREE (date),
			INDEX USING BTREE (url)
		) ENGINE = InnoDB CHARACTER SET ".$this->_charset." COLLATE ".$this->_collate.";");
		
		return $query;
	}

	/*
		Metoda prywatna, tworzy struktórę tabeli pages_types
	*/
	private function createPagesTypes() 
	{
		$query = $this->dbQuery("CREATE TABLE `".$this->_db_prefix."pages_types` (
			`id` SMALLINT UNSIGNED AUTO_INCREMENT,
			`name` VARCHAR(255) NOT NULL DEFAULT '',
			`for_news_page` TINYINT UNSIGNED NOT NULL DEFAULT '0',
			`user_allow_edit_own` TINYINT UNSIGNED NOT NULL DEFAULT '0',
			`user_allow_use_wysiwyg` TINYINT UNSIGNED NOT NULL DEFAULT '0',
			`insight_groups` VARCHAR(255) NOT NULL DEFAULT '',
			`editing_groups` VARCHAR(255) NOT NULL DEFAULT '',
			`submitting_groups` VARCHAR(255) NOT NULL DEFAULT '',
			`show_preview` TINYINT UNSIGNED NOT NULL DEFAULT '1',
			`add_author` TINYINT UNSIGNED NOT NULL DEFAULT '1',
			`change_author` TINYINT UNSIGNED NOT NULL DEFAULT '0',
			`add_last_editing_date` TINYINT UNSIGNED NOT NULL DEFAULT '0',
			`change_date` TINYINT UNSIGNED NOT NULL DEFAULT '0',
			`show_author` TINYINT UNSIGNED NOT NULL DEFAULT '1',
			`show_category` TINYINT UNSIGNED NOT NULL DEFAULT '1',
			`show_date` TINYINT UNSIGNED NOT NULL DEFAULT '1',
			`show_tags` TINYINT UNSIGNED NOT NULL DEFAULT '1',
			`show_type` TINYINT UNSIGNED NOT NULL DEFAULT '1',
			`user_allow_comments` TINYINT UNSIGNED NOT NULL DEFAULT '1',
			PRIMARY KEY (`id`)
		) ENGINE = InnoDB CHARACTER SET ".$this->_charset." COLLATE ".$this->_collate.";");
		
		return $query;
	}

	/*
		Metoda prywatna, tworzy struktórę tabeli pages_categories
	*/
	private function createPagesCategories() 
	{
		$query_1 = $this->dbQuery("CREATE TABLE `".$this->_db_prefix."pages_categories` (
			`id` SMALLINT UNSIGNED AUTO_INCREMENT,
			`name` VARCHAR(255) NOT NULL DEFAULT '',
			`description` MEDIUMTEXT NOT NULL,
			`submitting_groups` VARCHAR(255) NOT NULL DEFAULT '',
			`thumbnail` VARCHAR(255) NOT NULL DEFAULT '',
			`is_system` TINYINT UNSIGNED NOT NULL DEFAULT '0',
			PRIMARY KEY (`id`),
			INDEX USING BTREE (name)
		) ENGINE = InnoDB CHARACTER SET ".$this->_charset." COLLATE ".$this->_collate.";");
		
		$query_2 = $this->dbQuery("INSERT INTO `".$this->_db_prefix."pages_categories` (`name`, `description`, `submitting_groups`, `is_system`) VALUES ('".$this->_lang['No category']."', '".$this->_lang['No category desc']."', 0, 1)");

		return ($query_1 && $query_2 ? TRUE : FALSE);
	}

	/*
		Metoda prywatna, tworzy struktórę tabeli pages_custom_settings
	*/
	private function createPagesCustomSettings() 
	{
		$query = $this->dbQuery("CREATE TABLE `".$this->_db_prefix."pages_custom_settings` (
			`id` SMALLINT UNSIGNED AUTO_INCREMENT,
			`settings` MEDIUMTEXT NOT NULL,
			PRIMARY KEY (`id`)
		) ENGINE = InnoDB CHARACTER SET ".$this->_charset." COLLATE ".$this->_collate.";");
		
		return $query;
	}

	/*
		Metoda prywatna, tworzy struktórę tabeli time_formats
	*/
	private function createTimeFormats() 
	{
		$query = $this->dbQuery("CREATE TABLE `".$this->_db_prefix."time_formats` (
			`id` MEDIUMINT UNSIGNED NOT NULL AUTO_INCREMENT,
			`value` VARCHAR(100) NOT NULL DEFAULT '',
			PRIMARY KEY (`id`),
			UNIQUE KEY `value` (`value`)
		) ENGINE = InnoDB CHARACTER SET ".$this->_charset." COLLATE ".$this->_collate.";");
		
		return $query;
	}

	/*
		Metoda prywatna, usuwa nie potrzebne w systemie eXtreme-Fusion tabele
	*/
	private function dropOldTables() 
	{
		$query = $this->dbQuery("DROP TABLE IF EXISTS `".$this->_db_prefix."articles`");
		$query = $this->dbQuery("DROP TABLE IF EXISTS `".$this->_db_prefix."article_cats`");
		$query = $this->dbQuery("DROP TABLE IF EXISTS `".$this->_db_prefix."captcha`");
		$query = $this->dbQuery("DROP TABLE IF EXISTS `".$this->_db_prefix."custom_pages`");
		$query = $this->dbQuery("DROP TABLE IF EXISTS `".$this->_db_prefix."downloads`");
		$query = $this->dbQuery("DROP TABLE IF EXISTS `".$this->_db_prefix."download_cats`");
		$query = $this->dbQuery("DROP TABLE IF EXISTS `".$this->_db_prefix."faqs`");
		$query = $this->dbQuery("DROP TABLE IF EXISTS `".$this->_db_prefix."faq_cats`");
		$query = $this->dbQuery("DROP TABLE IF EXISTS `".$this->_db_prefix."forums`");
		$query = $this->dbQuery("DROP TABLE IF EXISTS `".$this->_db_prefix."forum_attachments`");
		$query = $this->dbQuery("DROP TABLE IF EXISTS `".$this->_db_prefix."flood_control`");
		$query = $this->dbQuery("DROP TABLE IF EXISTS `".$this->_db_prefix."infusions`");
		$query = $this->dbQuery("DROP TABLE IF EXISTS `".$this->_db_prefix."messages_options`");
		$query = $this->dbQuery("DROP TABLE IF EXISTS `".$this->_db_prefix."new_users`");
		$query = $this->dbQuery("DROP TABLE IF EXISTS `".$this->_db_prefix."photos`");
		$query = $this->dbQuery("DROP TABLE IF EXISTS `".$this->_db_prefix."photo_albums`");
		$query = $this->dbQuery("DROP TABLE IF EXISTS `".$this->_db_prefix."poll_votes`");
		$query = $this->dbQuery("DROP TABLE IF EXISTS `".$this->_db_prefix."polls`");
		$query = $this->dbQuery("DROP TABLE IF EXISTS `".$this->_db_prefix."posts`");
		$query = $this->dbQuery("DROP TABLE IF EXISTS `".$this->_db_prefix."ratings`");
		$query = $this->dbQuery("DROP TABLE IF EXISTS `".$this->_db_prefix."submissions`");
		$query = $this->dbQuery("DROP TABLE IF EXISTS `".$this->_db_prefix."vcode`");
		$query = $this->dbQuery("DROP TABLE IF EXISTS `".$this->_db_prefix."buttons`");
		$query = $this->dbQuery("DROP TABLE IF EXISTS `".$this->_db_prefix."cautions`");
		$query = $this->dbQuery("DROP TABLE IF EXISTS `".$this->_db_prefix."cautions_config`");
		$query = $this->dbQuery("DROP TABLE IF EXISTS `".$this->_db_prefix."colors`"); 
		$query = $this->dbQuery("DROP TABLE IF EXISTS `".$this->_db_prefix."panels_article`");
		$query = $this->dbQuery("DROP TABLE IF EXISTS `".$this->_db_prefix."panels_download`");
		$query = $this->dbQuery("DROP TABLE IF EXISTS `".$this->_db_prefix."panels_forum`");
		$query = $this->dbQuery("DROP TABLE IF EXISTS `".$this->_db_prefix."rss_builder`");
		$query = $this->dbQuery("DROP TABLE IF EXISTS `".$this->_db_prefix."site_links`");
		$query = $this->dbQuery("DROP TABLE IF EXISTS `".$this->_db_prefix."site_links_groups`");
		$query = $this->dbQuery("DROP TABLE IF EXISTS `".$this->_db_prefix."threads`");
		$query = $this->dbQuery("DROP TABLE IF EXISTS `".$this->_db_prefix."thread_notify`");
		$query = $this->dbQuery("DROP TABLE IF EXISTS `".$this->_db_prefix."user_groups`");
		$query = $this->dbQuery("DROP TABLE IF EXISTS `".$this->_db_prefix."eps_points`");
		$query = $this->dbQuery("DROP TABLE IF EXISTS `".$this->_db_prefix."eps_rangs`");
		$query = $this->dbQuery("DROP TABLE IF EXISTS `".$this->_db_prefix."forumrang`");
		$query = $this->dbQuery("DROP TABLE IF EXISTS `".$this->_db_prefix."weblinks`");
		$query = $this->dbQuery("DROP TABLE IF EXISTS `".$this->_db_prefix."weblink_cats`");

		return $query;
	}
	
	/*
		Metoda prywatna, zmienia struktórę tabeli blacklist
	*/
	private function changeBlacklistFields() 
	{
		$query_1 = $this->dbQuery("ALTER TABLE `".$this->_db_prefix."blacklist`
			CHANGE `blacklist_id` `id` MEDIUMINT UNSIGNED NOT NULL AUTO_INCREMENT, 
			CHANGE `blacklist_ip` `ip` VARCHAR(45) NOT NULL DEFAULT '',
			CHANGE `blacklist_email` `email` VARCHAR(100) NOT NULL DEFAULT '',
			CHANGE `blacklist_reason` `reason` TEXT NOT NULL DEFAULT '',
			ADD `user_id` MEDIUMINT UNSIGNED NOT NULL DEFAULT '0' AFTER `id`,
			ADD `type` TINYINT(3) UNSIGNED NOT NULL DEFAULT '4' AFTER `ip`,
			ADD `datestamp` INT(10) UNSIGNED NOT NULL DEFAULT '0' AFTER `reason`,
			ADD	INDEX `type` (`type`), 
			ENGINE = InnoDB	DEFAULT CHARACTER SET ".$this->_charset." COLLATE ".$this->_collate.";
		");
		
		$query_2 = $this->dbQuery("UPDATE `".$this->_db_prefix."blacklist` SET `user_id` = '1' WHERE `user_id` = '0'");
		
		$query_3 = $this->dbQuery("UPDATE `".$this->_db_prefix."blacklist` SET `datestamp` = '".time()."' WHERE `datestamp` = '0'");
		
		return ($query_1 && $query_2 && $query_3 ? TRUE : FALSE);
	}
	
	/*
		Metoda prywatna, zmienia struktórę tabeli comments
	*/
	private function changeCommentsFields() 
	{
		$query = $this->dbQuery("ALTER TABLE `".$this->_db_prefix."comments`
			DROP `comment_smileys`,
			CHANGE `comment_id` `id` MEDIUMINT UNSIGNED NOT NULL AUTO_INCREMENT ,
			CHANGE `comment_item_id` `content_id` MEDIUMINT UNSIGNED NOT NULL DEFAULT '0',
			CHANGE `comment_type` `content_type` VARCHAR(20) NOT NULL DEFAULT '',
			CHANGE `comment_name` `author` VARCHAR(50) NOT NULL DEFAULT '',
			CHANGE `comment_datestamp` `datestamp` INT UNSIGNED NOT NULL DEFAULT '0',
			CHANGE `comment_ip` `ip` VARCHAR(20) NOT NULL DEFAULT '0.0.0.0',
			CHANGE `comment_message` `post` TEXT NOT NULL AFTER `content_type`,
			ADD `author_type` VARCHAR(1) NOT NULL DEFAULT '' AFTER `author`,
			ADD INDEX `datestamp` (`datestamp`), 
			ENGINE = InnoDB	DEFAULT CHARACTER SET ".$this->_charset." COLLATE ".$this->_collate.";
		");
	
		$q = $this->dbQuery("SELECT `id`, `author`, `content_type` FROM `".$this->_db_prefix."comments`"); $i = 0;
		while ($data = $this->dbArray($q)) 
		{
			if ($data['content_type'] === 'N')
			{
				$this->dbQuery("UPDATE `".$this->_db_prefix."comments` 
					SET `content_type` = 'news'
					WHERE `id` = '".$data['id']."'
				");
			}
			else
			{
				$this->dbQuery("DELETE FROM `".$this->_db_prefix."comments` WHERE `id` = '".$data['id']."'");
			}
			if (is_numeric($data['author']))
			{
				$this->dbQuery("UPDATE `".$this->_db_prefix."comments` 
					SET `author_type` = 'u'
					WHERE `id` = '".$data['id']."'
				");
			}
			else
			{
				$this->dbQuery("UPDATE `".$this->_db_prefix."comments` 
					SET `author_type` = 'g'
					WHERE `id` = '".$data['id']."'
				");
			}
			$i++;
		}
	
		return $query;
	}
		
	/*
		Metoda prywatna, zmienia struktórę tabeli messages
	*/
	private function changeMessagesFields() 
	{
		
		$query = $this->dbQuery("ALTER TABLE `".$this->_db_prefix."messages`
			CHANGE `message_id` `id` MEDIUMINT UNSIGNED NOT NULL AUTO_INCREMENT,
			CHANGE `message_to` `to` MEDIUMINT UNSIGNED NOT NULL DEFAULT '0',
			CHANGE `message_from` `from` MEDIUMINT UNSIGNED NOT NULL DEFAULT '0',
			CHANGE `message_subject` `subject` VARCHAR(100) NOT NULL DEFAULT '',
			CHANGE `message_message` `message` TEXT NOT NULL,
			CHANGE `message_read` `read` TINYINT UNSIGNED NOT NULL DEFAULT '0',
			CHANGE `message_datestamp` `datestamp` INT UNSIGNED NOT NULL DEFAULT '0',
			DROP `message_smileys`,
			DROP `message_folder`,
			ADD `item_id` MEDIUMINT UNSIGNED NOT NULL DEFAULT '0' AFTER `id`,
			ADD INDEX `datestamp` (`datestamp`), 
			ENGINE = InnoDB	DEFAULT CHARACTER SET ".$this->_charset." COLLATE ".$this->_collate.";
		");
		
		return $query;
	}
	
	/*
		Metoda prywatna, zmienia struktórę tabeli news
	*/
	private function changeNewsFields() 
	{
		$query = $this->dbQuery("ALTER TABLE `".$this->_db_prefix."news` 
			DROP `news_rss`,
			DROP `news_start`,
			DROP `news_end`,
			CHANGE `news_id` `id` MEDIUMINT UNSIGNED NOT NULL AUTO_INCREMENT,
			CHANGE `news_subject` `title` VARCHAR(255) NOT NULL DEFAULT '',
			CHANGE `news_cat` `category` MEDIUMINT UNSIGNED NOT NULL DEFAULT '0',
			CHANGE `news_reads` `reads` INT UNSIGNED NOT NULL DEFAULT '0',
			CHANGE `news_news` `content` TEXT NOT NULL,
			CHANGE `news_extended` `content_extended` TEXT NOT NULL,
			CHANGE `news_breaks` `breaks` CHAR(1) NOT NULL DEFAULT '',
			CHANGE `news_datestamp` `datestamp` INT UNSIGNED NOT NULL DEFAULT '0',
			CHANGE `news_sticky` `sticky` TINYINT UNSIGNED NOT NULL DEFAULT '0',
			CHANGE `news_allow_comments` `allow_comments` TINYINT UNSIGNED NOT NULL DEFAULT '1',
			CHANGE `news_allow_ratings` `allow_ratings` TINYINT UNSIGNED NOT NULL DEFAULT '1',
			CHANGE `news_visibility` `access` VARCHAR(255) NOT NULL DEFAULT '' AFTER `datestamp`,
			CHANGE `news_name` `author` MEDIUMINT UNSIGNED NOT NULL DEFAULT '0' AFTER `content_extended`,
			ADD `draft` TINYINT UNSIGNED NOT NULL DEFAULT '0' AFTER `reads`,
			ADD `language` VARCHAR(255) NOT NULL DEFAULT 'English' AFTER `category`,
			ADD `link` VARCHAR(255) NOT NULL DEFAULT '' AFTER `title`,
			ADD `source` TEXT NOT NULL AFTER `author`,
			ADD `description` TEXT NOT NULL AFTER `source`,
			ADD INDEX `datestamp` (`datestamp`),
			ADD INDEX `reads` (`reads`), 
			ENGINE = InnoDB	DEFAULT CHARACTER SET ".$this->_charset." COLLATE ".$this->_collate.";
		");
		
		if ($query)
		{
			// Aktualizacja widoczności newsów dla podstawowych grup
			$this->dbQuery("UPDATE `".$this->_db_prefix."news` SET `access` = '3' WHERE `access` = '0'");
			$this->dbQuery("UPDATE `".$this->_db_prefix."news` SET `access` = '2' WHERE `access` = '101'");
			$this->dbQuery("UPDATE `".$this->_db_prefix."news` SET `access` = '1' WHERE `access` = '102' OR `access` = '103'");
		}
		
		return $query;
	}
	
	/*
		Metoda prywatna, zmienia struktórę tabeli news_cats
	*/
	private function changeNewsCatsFields() 
	{
		$query = $this->dbQuery("ALTER TABLE `".$this->_db_prefix."news_cats`
			CHANGE `news_cat_id` `id` MEDIUMINT UNSIGNED NOT NULL AUTO_INCREMENT,
			CHANGE `news_cat_name` `name` VARCHAR(100) NOT NULL DEFAULT '',
			CHANGE `news_cat_image` `image` VARCHAR(100) NOT NULL DEFAULT '',
			ADD `link` VARCHAR(100) NOT NULL DEFAULT '' AFTER `name`, 
			ENGINE = InnoDB	DEFAULT CHARACTER SET ".$this->_charset." COLLATE ".$this->_collate.";
		");
		
		if ($query)
		{
			$this->dbQuery("UPDATE `".$this->_db_prefix."news_cats` SET `image` = 'bugs.png' WHERE `image` = 'bugs.gif'");
			$this->dbQuery("UPDATE `".$this->_db_prefix."news_cats` SET `image` = 'downloads.png' WHERE `image` = 'downloads.gif'");
			$this->dbQuery("UPDATE `".$this->_db_prefix."news_cats` SET `image` = 'eXtreme-fusion.png', `name` = 'eXtreme-fusion' WHERE `image` = 'php-fusion.gif'");
			$this->dbQuery("UPDATE `".$this->_db_prefix."news_cats` SET `image` = 'eXtreme-fusion.png' WHERE `image` = 'eXtreme-fusion.gif'");
			$this->dbQuery("UPDATE `".$this->_db_prefix."news_cats` SET `image` = 'games.png' WHERE `image` = 'games.gif'");
			$this->dbQuery("UPDATE `".$this->_db_prefix."news_cats` SET `image` = 'graphics.png' WHERE `image` = 'graphics.gif'");
			$this->dbQuery("UPDATE `".$this->_db_prefix."news_cats` SET `image` = 'hardware.png' WHERE `image` = 'hardware.gif'");
			$this->dbQuery("UPDATE `".$this->_db_prefix."news_cats` SET `image` = 'journal.png' WHERE `image` = 'journal.gif'");
			$this->dbQuery("UPDATE `".$this->_db_prefix."news_cats` SET `image` = 'users.png' WHERE `image` = 'users.gif'");
			$this->dbQuery("UPDATE `".$this->_db_prefix."news_cats` SET `image` = 'mods.png' WHERE `image` = 'mods.gif'");
			$this->dbQuery("UPDATE `".$this->_db_prefix."news_cats` SET `image` = 'movies.png' WHERE `image` = 'movies.gif'");
			$this->dbQuery("UPDATE `".$this->_db_prefix."news_cats` SET `image` = 'network.png' WHERE `image` = 'network.gif'");
			$this->dbQuery("UPDATE `".$this->_db_prefix."news_cats` SET `image` = 'news.png' WHERE `image` = 'news.gif'");
			$this->dbQuery("UPDATE `".$this->_db_prefix."news_cats` SET `image` = 'security.png' WHERE `image` = 'security.gif'");
			$this->dbQuery("UPDATE `".$this->_db_prefix."news_cats` SET `image` = 'software.png' WHERE `image` = 'software.gif'");
			$this->dbQuery("UPDATE `".$this->_db_prefix."news_cats` SET `image` = 'themes.png' WHERE `image` = 'themes.gif'");
		}
		
		return $query;
	}
	
	/*
		Metoda prywatna, zmienia struktórę tabeli online
	*/
	private function changeOnlineFields() 
	{
		$query = $this->dbQuery("RENAME TABLE `".$this->_db_prefix."online` TO `".$this->_db_prefix."statistics`");
		
		if($query)
		{
			$query = $this->dbQuery("ALTER TABLE `".$this->_db_prefix."statistics`
				DROP `online_lastactive`,
				CHANGE `online_user` `id` MEDIUMINT UNSIGNED NOT NULL AUTO_INCREMENT,
				CHANGE `online_ip` `ip` VARCHAR(45) NOT NULL DEFAULT '0.0.0.0',		
				ADD PRIMARY KEY (`id`),
				ADD UNIQUE KEY `ip` (`ip`), 
				ENGINE = InnoDB	DEFAULT CHARACTER SET ".$this->_charset." COLLATE ".$this->_collate.";
			");	
		}

		return $query;
	}

	/*
		Metoda prywatna, zmienia struktórę tabeli panels
	*/
	private function changePanelsFields() 
	{
		$query_1 = $this->dbQuery("ALTER TABLE `".$this->_db_prefix."panels`
			CHANGE `panel_id` `id` MEDIUMINT UNSIGNED NOT NULL AUTO_INCREMENT,
			CHANGE `panel_name` `name` VARCHAR(100) NOT NULL DEFAULT '',
			CHANGE `panel_filename` `filename` VARCHAR(100) NOT NULL DEFAULT '',
			CHANGE `panel_content` `content` TEXT NOT NULL,
			CHANGE `panel_side` `side` TINYINT UNSIGNED NOT NULL DEFAULT '1',
			CHANGE `panel_order` `order` SMALLINT UNSIGNED NOT NULL DEFAULT '0',
			CHANGE `panel_type` `type` VARCHAR(20) NOT NULL DEFAULT '',
			CHANGE `panel_access` `access` VARCHAR(255) NOT NULL DEFAULT '',
			CHANGE `panel_display` `display` TINYINT UNSIGNED NOT NULL DEFAULT '0',
			CHANGE `panel_status` `status` TINYINT UNSIGNED NOT NULL DEFAULT '0',
			ADD KEY `order` (`order`), 
			ENGINE = InnoDB	DEFAULT CHARACTER SET ".$this->_charset." COLLATE ".$this->_collate.";
		");
		
		$query_2 = $this->dbQuery("TRUNCATE `".$this->_db_prefix."panels`");
		
		$query_3 = $this->dbQuery("INSERT INTO `".$this->_db_prefix."panels` (`name`, `filename`, `content`, `side`, `order`, `type`, `access`, `display`, `status`) VALUES 
			('".$this->_lang['Navigation']."', 'navigation_panel', '', '1', '1', 'file', '3', '0', '1'),
			('".$this->_lang['Online Users']."', 'online_users_panel', '', '1', '2', 'file', '3', '0', '1'),
			('".$this->_lang['Welcome Message']."', 'welcome_message_panel', '', '2', '1', 'file', '3', '0', '0'),
			('".$this->_lang['User Panel']."', 'user_info_panel', '', '4', 1, 'file', '3', '0', '1')
		");
		
		$query_4 = $this->dbQuery("UPDATE `".$this->_db_prefix."panels` SET status='1' WHERE filename='navigation_panel'");

		return ($query_1 && $query_2 && $query_3 && $query_4 ? TRUE : FALSE);
	}
	
	/*
		Metoda prywatna, zmienia struktórę tabeli smileys
	*/
	private function changeSmileysFields() 
	{
		$query_1 = $this->dbQuery("ALTER TABLE `".$this->_db_prefix."smileys`
			CHANGE `smiley_id` `id` MEDIUMINT UNSIGNED NOT NULL AUTO_INCREMENT,
			CHANGE `smiley_code` `code` VARCHAR(50) NOT NULL,
			CHANGE `smiley_image` `image` VARCHAR(100) NOT NULL,
			CHANGE `smiley_text` `text` VARCHAR(100) NOT NULL, 
			ENGINE = InnoDB	DEFAULT CHARACTER SET ".$this->_charset." COLLATE ".$this->_collate.";
		");
		
		$query_2 = $this->dbQuery("TRUNCATE `".$this->_db_prefix."smileys`");
		
		$query_3 = $this->dbQuery("INSERT INTO `".$this->_db_prefix."smileys` (`code`, `image`, `text`) VALUES 
			(':)', 'smile.png', 'Smile'),
			(';)', 'wink.png', 'Wink'),
			(':(', 'sad.png', 'Sad'),
			(';(', 'cry.png', 'Cry'),
			(':|', 'frown.png', 'Frown'),
			(':o', 'shock.png', 'Shock'),
			('Oo', 'blink.png', 'Blink'),
			(':P', 'pfft.png', 'Pfft'),
			('B)', 'cool.png', 'Cool'),
			(';/', 'annoyed.png', 'Annoyed'),
			(':D', 'grin.png', 'Grin'),
			(':@', 'angry.png', 'Angry'),
			('^^', 'joyful.png', 'Joyful'),
			('-.-', 'pinch.png', 'Pinch'),
			(':extreme:', '../favicon.ico', 'eXtreme-Fusion')
		");
		
		return ($query_1 && $query_2 && $query_3 ? TRUE : FALSE);
	}

	/*
		Metoda prywatna, zmienia struktórę tabeli users
	*/
	private function changeUsersFields() 
	{
		$query_1 = $this->dbQuery("ALTER TABLE `".$this->_db_prefix."users`
			CHANGE `user_id` `id` MEDIUMINT UNSIGNED NOT NULL AUTO_INCREMENT,
			CHANGE `user_name` `username` VARCHAR(30) NOT NULL DEFAULT '',
			CHANGE `user_password` `password` CHAR(129) NOT NULL DEFAULT '',
			ADD `salt` CHAR(5) NOT NULL DEFAULT '' AFTER `password`,
			ADD `user_hash` VARCHAR(10) NOT NULL DEFAULT '' AFTER `salt`,
			ADD `user_last_logged_in` INT UNSIGNED NOT NULL DEFAULT '0' AFTER `user_hash`,
			ADD `user_remember_me` TINYINT UNSIGNED NOT NULL DEFAULT '0' AFTER `user_last_logged_in`,
			ADD `admin_hash` VARCHAR(10) NOT NULL DEFAULT '' AFTER `user_remember_me`,
			ADD `admin_last_logged_in` INT UNSIGNED NOT NULL DEFAULT '0' AFTER `admin_hash`,
			ADD `browser_info` VARCHAR(100) NOT NULL DEFAULT '' AFTER `admin_last_logged_in`,
			ADD `link` VARCHAR(30) NOT NULL DEFAULT '' AFTER `browser_info`,
			CHANGE `user_email` `email` VARCHAR(100) NOT NULL DEFAULT '',
			CHANGE `user_hide_email` `hide_email` TINYINT UNSIGNED NOT NULL DEFAULT '1',
			ADD `valid_code` VARCHAR(32) NOT NULL DEFAULT '' AFTER `hide_email`,
			ADD `valid` TINYINT NOT NULL DEFAULT '0' AFTER `valid_code`,
			CHANGE `user_offset` `offset` CHAR(5) NOT NULL DEFAULT '0' AFTER `valid`,
			CHANGE `user_avatar` `avatar` VARCHAR(100) NOT NULL DEFAULT '' AFTER `offset`,
			CHANGE `user_joined` `joined` INT UNSIGNED NOT NULL DEFAULT '0' AFTER `avatar`,
			CHANGE `user_lastvisit` `lastvisit` INT UNSIGNED NOT NULL DEFAULT '0' AFTER `joined`,
			ADD `datestamp` INT UNSIGNED NOT NULL DEFAULT '0' AFTER `lastvisit`,
			CHANGE `user_ip` `ip` VARCHAR(20) NOT NULL DEFAULT '0.0.0.0' AFTER `datestamp`,
			CHANGE `user_status` `status` TINYINT UNSIGNED NOT NULL DEFAULT '0' AFTER `ip`,
			ADD `actiontime` INT UNSIGNED NOT NULL DEFAULT '0' AFTER `status`,
			CHANGE `user_theme` `theme` VARCHAR(100) NOT NULL DEFAULT 'Default',
			ADD `roles` TEXT NOT NULL AFTER `theme`,
			ADD `role` INT(11) NOT NULL DEFAULT '2' AFTER `roles`,
			ADD `lastupdate` INT NOT NULL DEFAULT '0' AFTER `role`,
			ADD `lang` VARCHAR(20) NOT NULL AFTER `lastupdate`,
			DROP `user_aim`,
			DROP `user_icq`,
			DROP `user_msn`,
			DROP `user_yahoo`,
			DROP `user_web`,
			DROP `user_sig`,
			DROP `user_posts`,
			DROP `user_rights`,
			DROP `user_groups`,
			DROP `user_birthdate`,
			DROP `user_location`,
			DROP `user_prefix`,
			DROP `user_color`,
			DROP `user_adds`,
			DROP `user_email_act`,
			DROP `user_rang`,
			DROP `user_points`,
			DROP `points_normal`,
			DROP `points_bonus`,
			DROP `points_punishment`,
			ADD INDEX `username` (`username`),
			ADD INDEX `joined` (`joined`),
			ADD INDEX `lastvisit` (`lastvisit`),
			ENGINE = InnoDB	DEFAULT CHARACTER SET ".$this->_charset." COLLATE ".$this->_collate.";
		");
		

		// Główny administrator powinien zadbać o skontrolowanie każdego z administratorów w celu korekt.
		// Minowanie administratorów o levelu 103 do rangi administratora
		$query_2 = $this->dbQuery("UPDATE `".$this->_db_prefix."users` 
			SET `roles` = 'a:3:{i:0;i:1;i:1;i:2;i:2;i:3;}', `role` = '1'
			WHERE `user_level` = '103'
		");

		// Odwołanie administratorów drugiego rzędu do rangi użytkownika, oraz nadanie zwykłym użytkownikom uprawnień użytkownika
		$query_3 = $this->dbQuery("UPDATE `".$this->_db_prefix."users` 
			SET `roles` = '".serialize(array(2 => '0', 0 => '2', 1 => '3'))."', `role` = '2'
			WHERE `user_level` = '101' OR `user_level` = '102'
		");
		
		$query_4 = $this->dbQuery("ALTER TABLE `".$this->_db_prefix."users` DROP `user_level`");
		
		$query_5 = $this->dbQuery("UPDATE `".$this->_db_prefix."users` SET `role` = '2' WHERE `role` = ''");
		
		$query_6 = $this->dbQuery("UPDATE `".$this->_db_prefix."users` SET `lang` = 'Polish' WHERE `lang` = ''");
		
		return ($query_1 && $query_2 && $query_3 && $query_4 && $query_5 && $query_6 ? TRUE : FALSE);	
	}	
	
	/*
		Metoda prywatna, tworzy brakujące pola w ustawieniach, 
		usuwa nie potrzebne pola, 
		przekrztałca struktórę tabeli i zapisuje nowe ustawienia
	*/
	private function createSettings() 
	{
		// Pobranie aktualnego adresu z wszystkimi informacjami
		$siteurl = $this->getCurrentURL();
		
		// Przypisanie aktualnego adresu URL
		$url = parse_url($siteurl);
		
		// Pobranie aktualnego regulaminu
		$rules = $this->dbArray($this->dbQuery("SELECT `code` FROM `".$this->_db_prefix."rules`"));
		
		// Usunięcia, dodanie i modyfikacja tabel
		// Przygotowanie przed konwersją tabeli z ustawieniami
		$query = $this->dbQuery("ALTER TABLE `".$this->_db_prefix."settings`
			DROP `news_style`,
			DROP `validation_method`,
			DROP `numofshouts`,
			DROP `ep_version`,
			DROP `rss`,
			DROP `updatecheck_datestamp`,
			DROP `avatar_shadow`,
			DROP `update_version`,
			DROP `sbx_edit_user`,
			DROP `comment_accept`,
			DROP `recaptcha_status`,	
			DROP `recaptcha_comments`,
			DROP `recaptcha_contact`,
			DROP `recaptcha_register`,
			DROP `attachtypes`,
			DROP `siteurl`,
			DROP `timeoffset`,
			DROP `attachments`,
			DROP `attachmax`,
			DROP `counter`,
			DROP `flood_interval`,
			DROP `forumdate`,
			DROP `guestposts`,
			DROP `numofthreads`,
			DROP `photo_h`,
			DROP `photo_max_b`,
			DROP `photo_max_h`,
			DROP `photo_max_w`,
			DROP `photo_w`,
			DROP `recaptcha_glob_key`,
			DROP `recaptcha_priv_key`,
			DROP `subheaderdate`,
			DROP `thread_notify`,
			DROP `thumbs_per_page`,
			DROP `thumbs_per_row`,
			DROP `thumb_compression`,
			DROP `thumb_h`,
			DROP `thumb_w`,
			DROP `tinymce_enabled`,
			DROP `display_validation`,
			CHANGE `sitename` `site_name` VARCHAR(200) NOT NULL DEFAULT '',
			CHANGE `footer` `footer` VARCHAR(200) NOT NULL DEFAULT '<div style=\'text-align:center\'>Copyright &copy; 2005 - ".@date("Y")." by the eXtreme-Fusion Crew</div>',
			CHANGE `theme` `theme` VARCHAR(100) NOT NULL DEFAULT 'eXtreme-Fusion-5',
			CHANGE `siteemail` `contact_email` VARCHAR(100) NOT NULL DEFAULT '',
			CHANGE `siteusername` `site_username` VARCHAR(100) NOT NULL DEFAULT '',
			CHANGE `siteintro` `site_intro` VARCHAR(100) NOT NULL DEFAULT '',
			CHANGE `sitebanner` `site_banner` VARCHAR(100) NOT NULL DEFAULT '', 
			ADD `site_banner1` TEXT NOT NULL AFTER `site_banner`, 
			ADD `site_banner2` TEXT NOT NULL AFTER `site_banner1`,
			ADD `avatar_width` TEXT NOT NULL AFTER `site_banner2`, 
			ADD `avatar_height` TEXT NOT NULL AFTER `avatar_width`, 
			ADD `avatar_filesize` TEXT NOT NULL AFTER `avatar_height`,
			ADD `smtp_port` TEXT NOT NULL AFTER `smtp_host`,
			ADD `news_photo_w` TEXT NOT NULL AFTER `smtp_password`,
			ADD `news_photo_h` TEXT NOT NULL AFTER `news_photo_w`,
			ADD `news_image_frontpage` TEXT NOT NULL AFTER `news_photo_h`,
			ADD `news_image_readmore` TEXT NOT NULL AFTER `news_image_frontpage`,
			ADD `cookie_secure` TEXT NOT NULL AFTER `news_image_readmore`,
			ADD `cookie_domain` TEXT NOT NULL AFTER `cookie_secure`,
			ADD `cookie_patch` TEXT NOT NULL AFTER `cookie_domain`,
			ADD `cronjob_day` TEXT NOT NULL AFTER `cookie_patch`,
			ADD `cronjob_hour` TEXT NOT NULL AFTER `cronjob_day`,
			ADD `cronjob_templates_clean` TEXT NOT NULL AFTER `cronjob_hour`,
			ADD `license_agreement` TEXT NOT NULL AFTER `cronjob_templates_clean`,
			ADD `license_lastupdate` TEXT NOT NULL AFTER `license_agreement`,
			ADD `logger_active` TEXT NOT NULL AFTER `license_lastupdate`,
			ADD `logger_optimize_active` TEXT NOT NULL AFTER `logger_active`,
			ADD `logger_expire_days` TEXT NOT NULL AFTER `logger_optimize_active`,
			ADD `logger_save_removal_action` TEXT NOT NULL AFTER `logger_expire_days`,
			ADD `cache_active` TEXT NOT NULL AFTER `logger_save_removal_action`,
			ADD `cache_expire` TEXT NOT NULL AFTER `cache_active`,
			ADD `timezone` TEXT NOT NULL AFTER `cache_expire`,
			ADD `offset_timezone` TEXT NOT NULL AFTER `timezone`,
			ADD `user_custom_offset_timezone` TEXT NOT NULL AFTER `offset_timezone`,
			ADD `enable_deactivation` TEXT NOT NULL AFTER `enable_registration`,
			ADD `login_method` TEXT NOT NULL AFTER `enable_deactivation`,
			ADD `enable_terms` TEXT NOT NULL AFTER `login_method`,
			ADD `visits_counter_enabled` TEXT NOT NULL AFTER `enable_terms`,
			ADD `validation` TEXT NOT NULL AFTER `admin_activation`,
			ADD `hide_userprofiles` TEXT NOT NULL AFTER `validation`,
			ADD `userthemes` TEXT NOT NULL AFTER `hide_userprofiles`,
			ADD `language_detection` TEXT NOT NULL AFTER `keywords`,
			ADD `deactivation_action` TEXT NOT NULL AFTER `userthemes`,
			ADD `change_name` TEXT NOT NULL AFTER `userthemes`,
			ADD `maintenance_level` TEXT NOT NULL AFTER `maintenance_message`,
			ADD `maintenance_form` TEXT NOT NULL AFTER `maintenance_level`,
			ADD `default_search` TEXT NOT NULL AFTER `maintenance_form`,
			ADD `deactivation_period` TEXT NOT NULL AFTER `default_search`,
			ADD `deactivation_response` TEXT NOT NULL AFTER `deactivation_period`,
			ADD `news_cats_item_per_page` TEXT NOT NULL AFTER `deactivation_response`,
			ADD `news_per_page` TEXT NOT NULL AFTER `news_cats_item_per_page`,
			ADD `notes_per_page` TEXT NOT NULL AFTER `news_per_page`,
			ADD `users_per_page` TEXT NOT NULL AFTER `notes_per_page`,
			ADD `comments_per_page` TEXT NOT NULL AFTER `users_per_page`,
			ADD `notes` TEXT NOT NULL AFTER `version`,
			ADD `loging` TEXT NOT NULL AFTER `notes`,
			ADD `routing` TEXT NOT NULL AFTER `loging`,
			ADD `cache` TEXT NOT NULL AFTER `routing`
		");
		
		if ($query)
		{
			// Stworzenie tabeli tymczasowej na potrzeby zapisania starych danych
			$query = $this->dbQuery("DROP TABLE IF EXISTS `".$this->_db_prefix."settings_tmp`");
			$query = $this->dbQuery("CREATE TABLE `".$this->_db_prefix."settings_tmp`
				(
					`key` VARCHAR(100) NOT NULL DEFAULT '',
					`value` text NOT NULL,
					PRIMARY KEY (`key`)
				) ENGINE = InnoDB CHARACTER SET ".$this->_charset." COLLATE ".$this->_collate.";
			");
			
			// Pobranie aktualnych już zmodyfikowanych danych
			$settings = $this->dbArray($this->dbQuery("SELECT * FROM `".$this->_db_prefix."settings`"));

			// Umieszczenie nowych danych w tabeli tymczasowej ustawień
			foreach ($settings as $key => $value) 
			{
				$query = $this->dbQuery("INSERT INTO `".$this->_db_prefix."settings_tmp`
					(`key`, `value`) VALUES ('$key', '$value')
				");
			}
			
			// Usunięcie starej tabeli z ustawieniami
			$query = $this->dbQuery("DROP TABLE IF EXISTS `".$this->_db_prefix."settings`");
			
			// Usunięcie tabeli z regulaminem strony
			$query = $this->dbQuery("DROP TABLE IF EXISTS `".$this->_db_prefix."rules`");
			
			// Zmiana nazwy tabeli tymczasowej na użyteczną dla systemu
			$query = $this->dbQuery("RENAME TABLE `".$this->_db_prefix."settings_tmp` TO `".$this->_db_prefix."settings`");
			
			// Jeśli operacja wykonała się pomyślnie dodaj wymagane dane do ustawień
			if ($query)
			{
				// Zapytania aktualizujące ustawienia
				$query = $this->dbQuery("UPDATE ".$this->_db_prefix."settings SET `value` = 'news' WHERE `key` = 'opening_page'");
				$query = $this->dbQuery("UPDATE ".$this->_db_prefix."settings SET `value` = 'eXtreme-Fusion-5' WHERE `key` = 'theme'");
				$query = $this->dbQuery("UPDATE ".$this->_db_prefix."settings SET `value` = '0' WHERE `key` = 'language_detection'");
				$query = $this->dbQuery("UPDATE ".$this->_db_prefix."settings SET `value` = 'themes/eXtreme-Fusion-5/templates/images/header_logo.png' WHERE `key` = 'site_banner'");
				$query = $this->dbQuery("UPDATE ".$this->_db_prefix."settings SET `value` = '100' WHERE `key` = 'avatar_width'");
				$query = $this->dbQuery("UPDATE ".$this->_db_prefix."settings SET `value` = '100' WHERE `key` = 'avatar_height'");
				$query = $this->dbQuery("UPDATE ".$this->_db_prefix."settings SET `value` = '102400' WHERE `key` = 'avatar_filesize'");
				$query = $this->dbQuery("UPDATE ".$this->_db_prefix."settings SET `value` = '587' WHERE `key` = 'smtp_port'");
				$query = $this->dbQuery("UPDATE ".$this->_db_prefix."settings SET `value` = '400' WHERE `key` = 'news_photo_w'");
				$query = $this->dbQuery("UPDATE ".$this->_db_prefix."settings SET `value` = '300' WHERE `key` = 'news_photo_h'");
				$query = $this->dbQuery("UPDATE ".$this->_db_prefix."settings SET `value` = '0' WHERE `key` = 'news_image_frontpage'");
				$query = $this->dbQuery("UPDATE ".$this->_db_prefix."settings SET `value` = '0' WHERE `key` = 'news_image_readmore'");
				$query = $this->dbQuery("UPDATE ".$this->_db_prefix."settings SET `value` = '0' WHERE `key` = 'cookie_secure'");
				$query = $this->dbQuery("UPDATE ".$this->_db_prefix."settings SET `value` = '".$_SERVER['HTTP_HOST']."' WHERE `key` = 'cookie_domain'");
				$query = $this->dbQuery("UPDATE ".$this->_db_prefix."settings SET `value` = '/' WHERE `key` = 'cookie_patch'");
				$query = $this->dbQuery("UPDATE ".$this->_db_prefix."settings SET `value` = '".time()."' WHERE `key` = 'cronjob_day'");
				$query = $this->dbQuery("UPDATE ".$this->_db_prefix."settings SET `value` = '".time()."' WHERE `key` = 'cronjob_hour'");
				$query = $this->dbQuery("UPDATE ".$this->_db_prefix."settings SET `value` = '".time()."' WHERE `key` = 'cronjob_templates_clean'");
				$query = $this->dbQuery("UPDATE ".$this->_db_prefix."settings SET `value` = '".$rules['code']."' WHERE `key` = 'license_agreement'");
				$query = $this->dbQuery("UPDATE ".$this->_db_prefix."settings SET `value` = '".time()."' WHERE `key` = 'license_lastupdate'");
				$query = $this->dbQuery("UPDATE ".$this->_db_prefix."settings SET `value` = '1' WHERE `key` = 'logger_active'");
				$query = $this->dbQuery("UPDATE ".$this->_db_prefix."settings SET `value` = '1' WHERE `key` = 'logger_optimize_active'");
				$query = $this->dbQuery("UPDATE ".$this->_db_prefix."settings SET `value` = '50' WHERE `key` = 'logger_expire_days'");
				$query = $this->dbQuery("UPDATE ".$this->_db_prefix."settings SET `value` = '50' WHERE `key` = 'logger_save_removal_action'");
				$query = $this->dbQuery("UPDATE ".$this->_db_prefix."settings SET `value` = '0' WHERE `key` = 'cache_active'");
				$query = $this->dbQuery("UPDATE ".$this->_db_prefix."settings SET `value` = '86400' WHERE `key` = 'cache_expire'");
				$query = $this->dbQuery("UPDATE ".$this->_db_prefix."settings SET `value` = '0' WHERE `key` = 'bad_words_enabled'");
				$query = $this->dbQuery("UPDATE ".$this->_db_prefix."settings SET `value` = '' WHERE `key` = 'bad_words'");
				$query = $this->dbQuery("UPDATE ".$this->_db_prefix."settings SET `value` = '******' WHERE `key` = 'bad_word_replace'");
				$query = $this->dbQuery("UPDATE ".$this->_db_prefix."settings SET `value` = 'Europe/London' WHERE `key` = 'timezone'");
				$query = $this->dbQuery("UPDATE ".$this->_db_prefix."settings SET `value` = '1.0' WHERE `key` = 'offset_timezone'");
				$query = $this->dbQuery("UPDATE ".$this->_db_prefix."settings SET `value` = '0' WHERE `key` = 'user_custom_offset_timezone'");
				$query = $this->dbQuery("UPDATE ".$this->_db_prefix."settings SET `value` = '0' WHERE `key` = 'enable_deactivation'");
				$query = $this->dbQuery("UPDATE ".$this->_db_prefix."settings SET `value` = 'sessions' WHERE `key` = 'login_method'");
				$query = $this->dbQuery("UPDATE ".$this->_db_prefix."settings SET `value` = '0' WHERE `key` = 'enable_terms'");
				$query = $this->dbQuery("UPDATE ".$this->_db_prefix."settings SET `value` = '1' WHERE `key` = 'visits_counter_enabled'");
				$query = $this->dbQuery("UPDATE ".$this->_db_prefix."settings SET `value` = '".serialize(array('register' => '0'))."' WHERE `key` = 'validation'");
				$query = $this->dbQuery("UPDATE ".$this->_db_prefix."settings SET `value` = '0' WHERE `key` = 'hide_userprofiles'");
				$query = $this->dbQuery("UPDATE ".$this->_db_prefix."settings SET `value` = '1' WHERE `key` = 'userthemes'");
				$query = $this->dbQuery("UPDATE ".$this->_db_prefix."settings SET `value` = '0' WHERE `key` = 'deactivation_action'");
				$query = $this->dbQuery("UPDATE ".$this->_db_prefix."settings SET `value` = '0' WHERE `key` = 'change_name'");
				$query = $this->dbQuery("UPDATE ".$this->_db_prefix."settings SET `value` = '1' WHERE `key` = 'maintenance_level'");
				$query = $this->dbQuery("UPDATE ".$this->_db_prefix."settings SET `value` = '1' WHERE `key` = 'maintenance_form'");
				$query = $this->dbQuery("UPDATE ".$this->_db_prefix."settings SET `value` = 'all' WHERE `key` = 'default_search'");
				$query = $this->dbQuery("UPDATE ".$this->_db_prefix."settings SET `value` = '365' WHERE `key` = 'deactivation_period'");
				$query = $this->dbQuery("UPDATE ".$this->_db_prefix."settings SET `value` = '14' WHERE `key` = 'deactivation_response'");
				$query = $this->dbQuery("UPDATE ".$this->_db_prefix."settings SET `value` = '10' WHERE `key` = 'news_cats_item_per_page'");
				$query = $this->dbQuery("UPDATE ".$this->_db_prefix."settings SET `value` = '11' WHERE `key` = 'news_per_page'");
				$query = $this->dbQuery("UPDATE ".$this->_db_prefix."settings SET `value` = '4' WHERE `key` = 'notes_per_page'");
				$query = $this->dbQuery("UPDATE ".$this->_db_prefix."settings SET `value` = '10' WHERE `key` = 'users_per_page'");
				$query = $this->dbQuery("UPDATE ".$this->_db_prefix."settings SET `value` = '11' WHERE `key` = 'comments_per_page'");
				$query = $this->dbQuery("UPDATE ".$this->_db_prefix."settings SET `value` = '".$this->_new_ef_version."' WHERE `key` = 'version'");
				$query = $this->dbQuery("UPDATE ".$this->_db_prefix."settings SET `value` = '".serialize(array(
						'site_normal_loging_time' => 60*60*5,			// 5h
						'site_remember_loging_time' => 60*60*24*21,		// 21 days
						'admin_loging_time' => 60*30,					// 30min
						'user_active_time' => 60*5						// 5min
					)
				)."' WHERE `key` = 'loging'");
				$query = $this->dbQuery("UPDATE ".$this->_db_prefix."settings SET `value` = '".serialize(array(
						'param_sep' => '/',
						'main_sep' => '/',
						'url_ext' => '.html',
						'tpl_ext' => '.tpl',
						'logic_ext' => '.php',
						'ext_allowed' => '1'
					)
				)."' WHERE `key` = 'routing'");
				$query = $this->dbQuery("UPDATE ".$this->_db_prefix."settings SET `value` = '".serialize(array(
					//'expire_contact' => '3600',
					'expire_news' => '3600',
					'expire_news_cats' => '3600',
					'expire_pages' => '3600',
					'expire_profile' => '3600',
					//'expire_rules' => '3600',
					'expire_tags' => '3600',
					'expire_team' => '3600',
					'expire_users' => '3600'
					)
				)."' WHERE `key` = 'cache'");
			}
		}
		
		return $query;
	}
	
	
	/*
		Metoda prywatna, odpowiada za połączenie się z bazą i wykonie zapytań
	*/
	private function dbQuery($query) 
	{
		$result = @mysql_query($query);
		if ( ! $result) 
		{
			echo mysql_error();
			return FALSE;
		} 
		else
		{
			return $result;
		}
	}
	
	/*
		Metoda prywatna, odpowiada za przeliczenie rekordów
	*/
	private function dbRows($query) 
	{
		return @mysql_num_rows($query);
	}

	/*
		Metoda prywatna, Fetch-uje dane z bazy danych
	*/
	private function dbArray($query) 
	{
		$result = @mysql_fetch_assoc($query);
		if ( ! $result) 
		{
			echo mysql_error();
			return false;
		} 
		else
		{
			return $result;
		}
	}
	
	/*
		Metoda publiczna, zwraca numer wersji systemu eXtreme-Fusion
	*/
	public function geteXtremeFusionVersion() 
	{
		return $this->_ef_version;
	}
	
	/*
		Metoda publiczna, zwraca numer wersji systemu nowego systemu
	*/
	public function getNeweXtremeFusionVersion() 
	{
		return $this->_new_ef_version;
	}
	
	/*
		Metoda prywatna
	*/
	private function cleanurl($url) 
	{
		$bad_entities = array("&", "\"", "'", '\"', "\'", "<", ">", "(", ")");
		$safe_entities = array("&amp;", "", "", "", "", "", "", "", "");
		$url = str_replace($bad_entities, $safe_entities, $url);
		return $url;
	}

	/*
		Metoda prywatna, alias metody substr
	*/
	private function strleft($s1, $s2) 
	{
		return substr($s1, 0, strpos($s1, $s2));
	}
	
	/*
		Metoda prywatna, pobiera aktualny adres strony wraz z protokolem
	*/
	private function getCurrentURL() 
	{
		$s = empty($_SERVER["HTTPS"]) ? "" : ($_SERVER["HTTPS"] == "on") ? "s" : "";
		$protocol = $this->strleft(strtolower($_SERVER["SERVER_PROTOCOL"]), "/").$s;
		$port = ($_SERVER["SERVER_PORT"] == "80") ? "" : (":".$_SERVER["SERVER_PORT"]);
		return $protocol."://".$_SERVER['SERVER_NAME'].$port.(str_replace(basename($this->cleanurl($_SERVER['PHP_SELF'])), "", $_SERVER['REQUEST_URI']));
	}
	
	/*
		Metoda prywatna, łaczenie z bazą
	*/
	private function dbconnect() 
	{
		$db_connect = @mysql_connect($this->_db_host, $this->_db_user, $this->_db_pass);
		$db_select = @mysql_select_db($this->_db_name);
		@mysql_query("SET NAMES 'utf8'");
		@mysql_query("SET CHARSET 'utf8'");
		if ( ! $db_connect) 
		{
			die("<div style='font-family:Verdana;font-size:11px;text-align:center;'><b>Unable to establish connection to MySQL</b><br>".mysql_errno()." : ".mysql_error()."</div>");
		} 
		elseif ( ! $db_select) 
		{
			die("<div style='font-family:Verdana;font-size:11px;text-align:center;'><b>Unable to select MySQL database</b><br>".mysql_errno()." : ".mysql_error()."</div>");
		}
	}
}
?>