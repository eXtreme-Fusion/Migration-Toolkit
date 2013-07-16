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

if ( ! isset($_COOKIE['efc_core']))
{
	require_once '../../maincore.php';
}
else
{
	require_once '../../config.php';
}

$aid = (isset($_GET['aid']) ? $_GET['aid'] : '');

if (file_exists('../../infusions/migration_toolkit/locale/'.(isset($_COOKIE['efc_lang']) ? $_COOKIE['efc_lang'] : $settings['locale']).'.php')) 
{
	include '../../infusions/migration_toolkit/locale/'.(isset($_COOKIE['efc_lang']) ? $_COOKIE['efc_lang'] : $settings['locale']).'.php';
} 
else
{
	include '../../infusions/migration_toolkit/locale/English.php';
}

require_once '../../infusions/migration_toolkit/class/class.Converter.php';

$_EFC = New Converter($EFC_Locale, array($db_prefix, $db_host, $db_user, $db_pass, $db_name));

if ( ! isset($_GET['step']) || ! preg_match("/^([0-9]{1})$/D", $_GET['step']))
{ 
	$_GET['step'] = '0'; 
}
if ($_GET['step'] <> '0' && ! isset($_POST['check']))
{ 
	$_GET['step'] = '0';
}

echo "<!DOCTYPE html>
<html>
	<head>
		<title>Konwerter systemu eXtreme-Fusion ".$_EFC->geteXtremeFusionVersion()." na system eXtreme-Fusion ".$_EFC->getNeweXtremeFusionVersion()."</title>
		<meta charset='UTF-8'>
		<link href='../../infusions/migration_toolkit/stylesheet/main.css' media='screen' rel='stylesheet'>
	</head>
<body>
";

echo "<h4>".($_GET['step'] == '0' ? "Witaj w konwerterze systemu eXtreme-Fusion ".$_EFC->geteXtremeFusionVersion()." na system eXtreme-Fusion ".$_EFC->getNeweXtremeFusionVersion() : "Migracja krok: ".($_GET['step']))."</h4>";
echo "<div style='text-align:center; min-height: 500px;'>";
if((isset($_COOKIE['efc_core']) ? ! $_COOKIE['efc_core'] : ! iSUPERADMIN))
{
	echo "<p>Jeśli chcesz użyć konwertera, musisz być zalogowany oraz posiadać rangę Super Administratora!</p>";
	setcookie("efc_superamdin", TRUE, time() + 60);
}
else
{
	if($_GET['step'] == '0')
	{
		unset($_COOKIE["efc_".md5($aid)]);
		unset($_COOKIE["efc_lang"]);
		unset($_COOKIE["efc_vers"]);
		unset($_COOKIE["efc_core"]);
		unset($_COOKIE["efc_superamdin"]);
		if (isset($settings['ep_version']) && $settings['ep_version'] !== $_EFC->geteXtremeFusionVersion())
		{
			echo "<p>Jeśli chcesz użyć tego konwertera, musisz mieć zainstalowany CMS eXtreme-Fusion 4.17!</p>";
		}
		else
		{
			echo "
			<p class='bold status info'>Witaj w wtyczce, która pomoże Ci przekonwertować <a href='http://extreme-fusion.org/'>eXtreme-Fusion ".$_EFC->geteXtremeFusionVersion()."</a>, na system <a href='http://extreme-fusion.org/'>eXtreme-Fusion ".$_EFC->getNeweXtremeFusionVersion()."</a>. Jeśli jeszcze nie zrobiłeś kopii zapasowej bazy danych oraz plików na serwerze, teraz jest to najlepszy moment aby to uczynić, jeśli coś pójdzie nie tak będziesz mógł bez problemu przywrócić kopię bazy danych.</p>
			<p class='bold status error'>
				Pamiętaj, jeśli zaczniesz operacje bez posiadania kopi swojej bazy danych narażasz się na utratę cennych danych.<br />
				W przypadku gdy wtyczka napotkałaby błąd, Twoja strona mogła by działać nie stabilnie.<br />
				Dlatego zaleca się aby przed wykonywaniem tej czynności zaopatrzyć się w kopię całej bazy danych którą wykonasz takim narzędziem jak PHPMyAdmin, Chive, SQLBuddy itp.<br /><br /><br />
				W przypadku wystąpienie błędu proszę o kontakt z Autorem wtyczki, na pewno zrobi wszystko aby poprawić ten błąd. <br />
			</p>
			<p class='bold status valid'>
				Aby przejść do kolejnego kroku kliknij na przycisk <strong>\"Dalej\"</strong>.
			</p>
			</div><form method='post' class='center' action='".basename($_SERVER['PHP_SELF'])."?aid=".$aid."&amp;step=1'>
			<p><input type='submit' name='check' value='Dalej' /></p>
			</form>";
		}
	}
	elseif($_GET['step'] == '1')
	{	
		setcookie("efc_".md5($aid), $aid, time() + 60);
		setcookie("efc_lang", $settings['locale'], time() + 60);
		setcookie("efc_vers", $settings['ep_version'], time() + 60);
		setcookie("efc_core", TRUE, time() + 60);
		echo "</div><form method='post' class='center' action='".basename($_SERVER['PHP_SELF'])."?aid=".$aid."&amp;step=2'>
		<p><input type='submit' name='check' value='Dalej' /></p>
		</form>";
		
	}
	elseif($_GET['step'] == '2')
	{
		$r = $_EFC->stepNum(intval($_GET['step']));
		echo"<p class='bold status info'>Krok 1: Tworzenie wymaganych tabel w bazie danych.</p>";
		echo "<ul class='left'>";
		foreach ($r as $value)
		{
			if ($value['status'] === TRUE)
			{
				echo "<li class='green'>Tabela: ". $db_prefix.$value['name'] ." została utworzona.</li>";
			}
			else
			{
				echo "<li class='red'>Tabela: ". $db_prefix.$value['name'] ." nie została utworzona, prawdopodobnie już istnieje!</li>";
			}
		}
		echo "</ul>";
		echo "</div><form method='post' class='center' action='".basename($_SERVER['PHP_SELF'])."?aid=".$aid."&amp;step=3'>
		<p><input type='submit' name='check' value='Dalej' /></p>
		</form>";
	}
	elseif($_GET['step'] == '3')
	{
		
		$r = $_EFC->stepNum(intval($_GET['step']));
		echo"<p class='bold status info'>Krok 2: Tworzenie struktury ustawień.</p>";
		echo "<ul class='left'>";
		foreach ($r as $value)
		{
			if ($value['status'] === TRUE)
			{
				echo "<li class='green'>Tabela: ". $db_prefix.$value['name'] ." została przetworzona.</li>";
			}
			else
			{
				echo "<li class='red'>Tabela: ". $db_prefix.$value['name'] ." nie została przetworzona, zgłoś ten błąd!</li>";
			}
		}
		echo "</ul>";
		echo "</div><form method='post' class='center' action='".basename($_SERVER['PHP_SELF'])."?aid=".$aid."&amp;step=4'>
		<p><input type='submit' name='check' value='Dalej' /></p>
		</form>";
	}
	elseif($_GET['step'] == '4')
	{
		$r = $_EFC->stepNum(intval($_GET['step']));
		echo"<p class='bold status info'>Krok 3: Usunięcie nie potrzebnych tabel w bazie danych.</p>";
		echo "<ul class='left'>";
		foreach ($r as $value)
		{
			if ($value['status'] === TRUE)
			{
				echo "<li class='green'>".$db_prefix."articles</li>";
				echo "<li class='green'>".$db_prefix."article_cats</li>";
				echo "<li class='green'>".$db_prefix."custom_pages</li>";
				echo "<li class='green'>".$db_prefix."downloads</li>";
				echo "<li class='green'>".$db_prefix."download_cats</li>";
				echo "<li class='green'>".$db_prefix."faqs</li>";
				echo "<li class='green'>".$db_prefix."faq_cats</li>";
				echo "<li class='green'>".$db_prefix."forums</li>";
				echo "<li class='green'>".$db_prefix."forum_attachments</li>";
				echo "<li class='green'>".$db_prefix."flood_control</li>";
				echo "<li class='green'>".$db_prefix."infusions</li>";
				echo "<li class='green'>".$db_prefix."messages_options</li>";
				echo "<li class='green'>".$db_prefix."new_users</li>";
				echo "<li class='green'>".$db_prefix."photos</li>";
				echo "<li class='green'>".$db_prefix."photo_albums</li>";
				echo "<li class='green'>".$db_prefix."poll_votes</li>";
				echo "<li class='green'>".$db_prefix."polls</li>";
				echo "<li class='green'>".$db_prefix."posts</li>";
				echo "<li class='green'>".$db_prefix."ratings</li>";
				echo "<li class='green'>".$db_prefix."submissions</li>";
				echo "<li class='green'>".$db_prefix."vcode</li>";
				echo "<li class='green'>".$db_prefix."buttons</li>";
				echo "<li class='green'>".$db_prefix."cautions</li>";
				echo "<li class='green'>".$db_prefix."cautions_config</li>";
				echo "<li class='green'>".$db_prefix."colors</li>";
				echo "<li class='green'>".$db_prefix."panels_article</li>";
				echo "<li class='green'>".$db_prefix."panels_download</li>";
				echo "<li class='green'>".$db_prefix."panels_forum</li>";
				echo "<li class='green'>".$db_prefix."rss_builder</li>";
				echo "<li class='green'>".$db_prefix."site_links</li>";
				echo "<li class='green'>".$db_prefix."site_links_groups</li>";
				echo "<li class='green'>".$db_prefix."threads</li>";
				echo "<li class='green'>".$db_prefix."thread_notify</li>";
				echo "<li class='green'>".$db_prefix."user_groups</li>";
				echo "<li class='green'>".$db_prefix."eps_points</li>";
				echo "<li class='green'>".$db_prefix."eps_rangs</li>";
				echo "<li class='green'>".$db_prefix."forumrang</li>";
				echo "<li class='green'>".$db_prefix."weblinks</li>";
				echo "<li class='green'>".$db_prefix."weblink_cats</li>";
			}
			else
			{
				echo "<li class='red'>".$db_prefix."articles</li>";
				echo "<li class='red'>".$db_prefix."article_cats</li>";
				echo "<li class='red'>".$db_prefix."captcha</li>";
				echo "<li class='red'>".$db_prefix."custom_pages</li>";
				echo "<li class='red'>".$db_prefix."downloads</li>";
				echo "<li class='red'>".$db_prefix."download_cats</li>";
				echo "<li class='red'>".$db_prefix."faqs</li>";
				echo "<li class='red'>".$db_prefix."faq_cats</li>";
				echo "<li class='red'>".$db_prefix."forums</li>";
				echo "<li class='red'>".$db_prefix."forum_attachments</li>";
				echo "<li class='red'>".$db_prefix."flood_control</li>";
				echo "<li class='red'>".$db_prefix."infusions</li>";
				echo "<li class='red'>".$db_prefix."messages_options</li>";
				echo "<li class='red'>".$db_prefix."new_users</li>";
				echo "<li class='red'>".$db_prefix."photos</li>";
				echo "<li class='red'>".$db_prefix."photo_albums</li>";
				echo "<li class='red'>".$db_prefix."poll_votes</li>";
				echo "<li class='red'>".$db_prefix."polls</li>";
				echo "<li class='red'>".$db_prefix."posts</li>";
				echo "<li class='red'>".$db_prefix."ratings</li>";
				echo "<li class='red'>".$db_prefix."submissions</li>";
				echo "<li class='red'>".$db_prefix."vcode</li>";
				echo "<li class='red'>".$db_prefix."buttons</li>";
				echo "<li class='red'>".$db_prefix."cautions</li>";
				echo "<li class='red'>".$db_prefix."cautions_config</li>";
				echo "<li class='red'>".$db_prefix."colors</li>";
				echo "<li class='red'>".$db_prefix."panels_article</li>";
				echo "<li class='red'>".$db_prefix."panels_download</li>";
				echo "<li class='red'>".$db_prefix."panels_forum</li>";
				echo "<li class='red'>".$db_prefix."rss_builder</li>";
				echo "<li class='red'>".$db_prefix."site_links</li>";
				echo "<li class='red'>".$db_prefix."site_links_groups</li>";
				echo "<li class='red'>".$db_prefix."threads</li>";
				echo "<li class='red'>".$db_prefix."thread_notify</li>";
				echo "<li class='red'>".$db_prefix."user_groups</li>";
				echo "<li class='red'>".$db_prefix."eps_points</li>";
				echo "<li class='red'>".$db_prefix."eps_rangs</li>";
				echo "<li class='red'>".$db_prefix."forumrang</li>";
				echo "<li class='red'>".$db_prefix."weblinks</li>";
				echo "<li class='red'>".$db_prefix."weblink_cats</li>";
			}
		}
		echo "</ul>";
		echo "</div><form method='post' class='center' action='".basename($_SERVER['PHP_SELF'])."?aid=".$aid."&amp;step=5'>
		<p><input type='submit' name='check' value='Dalej' /></p>
		</form>";
	}
	elseif($_GET['step'] == '5')
	{
		echo"<p class='bold status info'>Krok 4: Przetwarzanie pozostałych tabel.</p>";
		$r = $_EFC->stepNum(intval($_GET['step']));
		echo "<ul class='left'>";
		foreach ($r as $value)
		{
			if ($value['status'] === TRUE)
			{
				echo "<li class='green'>Tabela: ". $db_prefix.$value['name'] ." została przetworzona.</li>";
			}
			else
			{
				echo "<li class='red'>Tabela: ". $db_prefix.$value['name'] ." nie została przetworzona, zgłoś ten błąd!</li>";
			}
		}
		echo "</ul>";
		echo "</div><form method='post' class='center' action='".basename($_SERVER['PHP_SELF'])."?aid=".$aid."&amp;step=6'>
		<p><input type='submit' name='check' value='Dalej' /></p>
		</form>";
	}
	elseif($_GET['step'] == '6')
	{
		echo"<p class='bold status info'>Krok 6: Przetwarzanie pozostałych tabel.</p>";
		$r = $_EFC->stepNum(intval($_GET['step']));
		echo "<ul class='left'>";
		foreach ($r as $value)
		{
			if ($value['status'] === TRUE)
			{
				echo "<li class='green'>Tabela: ". $db_prefix.$value['name'] ." została przetworzona.</li>";
			}
			else
			{
				echo "<li class='red'>Tabela: ". $db_prefix.$value['name'] ." nie została przetworzona, zgłoś ten błąd!</li>";
			}
		}
		echo "</ul>";
		echo "</div><form method='post' class='center' action='".basename($_SERVER['PHP_SELF'])."?aid=".$aid."&amp;step=7'>
		<p><input type='submit' name='check' value='Dalej' /></p>
		</form>";
	}
	elseif($_GET['step'] == '7')
	{
		echo"<p class='bold status info'>Krok 5: Przetwarzanie pozostałych tabel.</p>";
		$r = $_EFC->stepNum(intval($_GET['step']));
		echo "<ul class='left'>";
		foreach ($r as $value)
		{
			if ($value['status'] === TRUE)
			{
				echo "<li class='green'>Tabela: ". $db_prefix.$value['name'] ." została przetworzona.</li>";
			}
			else
			{
				echo "<li class='red'>Tabela: ". $db_prefix.$value['name'] ." nie została przetworzona, zgłoś ten błąd!</li>";
			}
		}
		echo "</ul>";
		echo "</div><form method='post' class='center' action='".basename($_SERVER['PHP_SELF'])."?aid=".$aid."&amp;step=8'>
		<p><input type='submit' name='check' value='Dalej' /></p>
		</form>";
	}
	elseif($_GET['step'] == '8')
	{
		echo"<p class='bold status info'>Krok 7: Przetwarzanie pozostałych tabel.</p>";
		$r = $_EFC->stepNum(intval($_GET['step']));
		echo "<ul class='left'>";
		foreach ($r as $value)
		{
			if ($value['status'] === TRUE)
			{
				echo "<li class='green'>Tabela: ". $db_prefix.$value['name'] ." została przetworzona.</li>";
			}
			else
			{
				echo "<li class='red'>Tabela: ". $db_prefix.$value['name'] ." nie została przetworzona, zgłoś ten błąd!</li>";
			}
		}
		echo "</ul>";
		echo "</div><form method='post' class='center' action='".basename($_SERVER['PHP_SELF'])."?aid=".$aid."&amp;step=9'>
		<p><input type='submit' name='check' value='Dalej' /></p>
		</form>";
	}
	elseif($_GET['step'] == '9')
	{
		echo "<p class='bold status valid'>Krok 10: Sukces.<br />";
		echo "Twój system został zaktualizowany do najnowszej wersji systemu eXtreme-Fusion.<br />";
		echo "Teraz możesz usunąć stare pliki z eXtreme-Fusion i wrzuciś pliki z nowego systemu eXtreme-Fusion.<br />";
		echo "Wyedytuj plik config.php zamieniając jego zawartość na:<br />";
		echo $_EFC->stepNum(intval($_GET['step']));
		echo "</p>";
		
		echo "<p class='bold status valid'><a href='../../index.php' class='green bold'>Gdy skończysz wrzucać pliki na swój serwer oraz dokonasz edycji pliku config.php będziesz mógł zobaczyć swoją nową stronę klikając na ten napis.</a></p>";
		
	}

}

echo "
<body>
</html>";
mysql_close();
ob_end_flush();
?>