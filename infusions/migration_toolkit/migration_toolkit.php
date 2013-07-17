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
require_once '../../infusions/migration_toolkit/class/class.Converter.php';

if ( ! isset($_COOKIE['efc_core']))
{
	require_once '../../maincore.php';
}
else
{
	require_once '../../config.php';
}
/*
if ( ! isset($settings['locale']))
{
	if (isset($_COOKIE["efc_".md5($aid)])) unset($_COOKIE["efc_".md5($aid)]);
	if (isset($_COOKIE["efc_lang"])) unset($_COOKIE["efc_lang"]);
	if (isset($_COOKIE["efc_vers"])) unset($_COOKIE["efc_vers"]);
	if (isset($_COOKIE["efc_core"])) unset($_COOKIE["efc_core"]);
	if (isset($_COOKIE["efc_superamdin"])) unset($_COOKIE["efc_superamdin"]);
}*/

$aid = (isset($_GET['aid']) ? $_GET['aid'] : '');

if (file_exists('../../infusions/migration_toolkit/locale/'.(isset($_COOKIE['efc_lang']) ? $_COOKIE['efc_lang'] : $settings['locale']).'.php')) 
{
	include '../../infusions/migration_toolkit/locale/'.(isset($_COOKIE['efc_lang']) ? $_COOKIE['efc_lang'] : $settings['locale']).'.php';
} 
else
{
	include '../../infusions/migration_toolkit/locale/English.php';
}

$_EFC = New Converter($EFC_Locale, array($db_prefix, $db_host, $db_user, $db_pass, $db_name));

$start_gen = $_EFC->getTime();

if ( ! isset($_GET['step']) || ! preg_match("/^([0-9]{1,2})$/D", $_GET['step']))
{ 
	$_GET['step'] = '1'; 
}
if ($_GET['step'] <> '1' && ! isset($_POST['check']))
{ 
	$_GET['step'] = '1';
}

$steps = array(
	1 => "Najważniejsze informacje przed instalacją",
	2 => "Tworzenie kopii bazy",
	3 => "Tworzenie nowych tabel",
	4 => "Tworzenie struktury ustawień",
	5 => "Usunięcie nie potrzebnych tabel",
	6 => "Przetwarzanie pozostałych tabel",
	7 => "Przetwarzanie pozostałych tabel",
	8 => "Przetwarzanie pozostałych tabel",
	9 => "Przetwarzanie pozostałych tabel",
	10 => "Sukces! Migracja zakończona",
);

$cookies = 360;

echo "
<!DOCTYPE html>
<html>
	<head>
		<meta charset='UTF-8'>
		<title>Migration Toolkit ".$_EFC->geteXtremeFusionVersion()." » ".$_EFC->getNeweXtremeFusionVersion()."</title>
		<link rel='stylesheet' href='../../infusions/migration_toolkit/stylesheet/grid.reset.css'>
		<link rel='stylesheet' href='../../infusions/migration_toolkit/stylesheet/grid.text.css'>
		<link rel='stylesheet' href='../../infusions/migration_toolkit/stylesheet/grid.960.css'>
		<link rel='stylesheet' href='../../infusions/migration_toolkit/stylesheet/jquery.uniform.css'>
		<link rel='stylesheet' href='../../infusions/migration_toolkit/stylesheet/jquery.table.css'>
		<link rel='stylesheet' href='../../infusions/migration_toolkit/stylesheet/jquery.ui.css'>
		<link rel='stylesheet' href='../../infusions/migration_toolkit/stylesheet/main.css'>
		<script src='../../infusions/migration_toolkit/javascripts/jquery.js'></script>
		<script src='../../infusions/migration_toolkit/javascripts/jquery.uniform.js'></script>
		<script src='../../infusions/migration_toolkit/javascripts/main.js'></script>
	</head>
	<body>
		<div style='background: #121212; height: 60px; margin-bottom: 16px; padding: 10px 0;'>
			<div class='container_12'>
				<img src='../../infusions/migration_toolkit/images/extreme-fusion-logo.png' alt='Migration Toolkit ".$_EFC->geteXtremeFusionVersion()." » ".$_EFC->getNeweXtremeFusionVersion()."'>
			</div>
		</div>	
			<div id='Content'>
			<div class='corner4px'><div class='ctl'><div class='ctr'><div class='ctc'></div></div></div><div class='cc'>
				<div id='IframeOPT' class='container_12' >



					<div class='clear'></div>
					<h3 class='ui-corner-all'>
						Migration Toolkit ".$_EFC->geteXtremeFusionVersion()." » ".$_EFC->getNeweXtremeFusionVersion()." | Migracja krok: ".$_GET['step']." » ".($steps[$_GET['step']])."
					</h3>

					<ul id='InstalationSteps'>
							<li class='".($_GET['step'] < '1' ? 'bold' : $_GET['step'] == '1' ? $_GET['step'] == '1' ? 'bold' : '' : 'done')."'>Krok 1</li>
							<li class='".($_GET['step'] < '2' ? 'bold' : $_GET['step'] == '2' ? $_GET['step'] == '2' ? 'bold' : '' : 'done')."'>Krok 2</li>
							<li class='".($_GET['step'] < '3' ? 'bold' : $_GET['step'] == '3' ? $_GET['step'] == '3' ? 'bold' : '' : 'done')."'>Krok 3</li>
							<li class='".($_GET['step'] < '4' ? 'bold' : $_GET['step'] == '4' ? $_GET['step'] == '4' ? 'bold' : '' : 'done')."'>Krok 4</li>
							<li class='".($_GET['step'] < '5' ? 'bold' : $_GET['step'] == '5' ? $_GET['step'] == '5' ? 'bold' : '' : 'done')."'>Krok 5</li>
							<li class='".($_GET['step'] < '6' ? 'bold' : $_GET['step'] == '6' ? $_GET['step'] == '6' ? 'bold' : '' : 'done')."'>Krok 6</li>
							<li class='".($_GET['step'] < '7' ? 'bold' : $_GET['step'] == '7' ? $_GET['step'] == '7' ? 'bold' : '' : 'done')."'>Krok 7</li>
							<li class='".($_GET['step'] < '8' ? 'bold' : $_GET['step'] == '8' ? $_GET['step'] == '8' ? 'bold' : '' : 'done')."'>Krok 8</li>
							<li class='".($_GET['step'] < '9' ? 'bold' : $_GET['step'] == '9' ? $_GET['step'] == '9' ? 'bold' : '' : 'done')."'>Krok 9</li>
							<li class='".($_GET['step'] < '10' ? 'bold' : $_GET['step'] == '10' ? $_GET['step'] == '10' ? 'bold' : '' : 'done')."'>Krok 10</li>
					</ul>

					<div id='MainBox'>";

					if((isset($_COOKIE['efc_core']) ? ! $_COOKIE['efc_core'] : ! iSUPERADMIN))
					{
						echo "<p class='formWarning center'>Jeśli chcesz użyć konwertera, musisz być zalogowany oraz posiadać rangę Super Administratora!</p>";
						setcookie("efc_superamdin", TRUE, time() + $cookies);
					}
					else
					{
						if($_GET['step'] == '1')
						{
							unset($_COOKIE["efc_".md5($aid)]);
							unset($_COOKIE["efc_lang"]);
							unset($_COOKIE["efc_vers"]);
							unset($_COOKIE["efc_core"]);
							unset($_COOKIE["efc_superamdin"]);
							if (isset($settings['ep_version']) && $settings['ep_version'] !== $_EFC->geteXtremeFusionVersion())
							{
								echo "<p class='formWarning center'>Jeśli chcesz użyć tego konwertera, musisz mieć zainstalowany CMS eXtreme-Fusion 4.17!</p>";
							}
							else
							{
								echo "
								<p class='formValid'>Witaj w wtyczce, która pomoże Ci przekonwertować <a href='http://extreme-fusion.org/'>eXtreme-Fusion ".$_EFC->geteXtremeFusionVersion()."</a>, na system <a href='http://extreme-fusion.org/'>eXtreme-Fusion ".$_EFC->getNeweXtremeFusionVersion()."</a>. Jeśli jeszcze nie zrobiłeś kopii zapasowej bazy danych oraz plików na serwerze, teraz jest to najlepszy moment aby to uczynić, jeśli coś pójdzie nie tak będziesz mógł bez problemu przywrócić kopię bazy danych.</p>
								<p class='formValid'>
									Pamiętaj, jeśli zaczniesz operacje bez posiadania kopi swojej bazy danych narażasz się na utratę cennych danych.<br />
									W przypadku gdy wtyczka napotkałaby błąd, Twoja strona mogła by działać nie stabilnie.<br />
									Dlatego zaleca się aby przed wykonywaniem tej czynności zaopatrzyć się w kopię całej bazy danych którą wykonasz takim narzędziem jak PHPMyAdmin, Chive, SQLBuddy itp.<br />
								</p>
								
								<p class='formValid'>Przed rozpoczęciem migracji proszę zapoznać się z licencją:<br /> <a id='AcceptLink' href='https://raw.github.com/extreme-fusion/eXtreme-Fusion-CMS/master/LICENSE' target='_blank'>aGPL v3 License</a></p>
								
								<p class='formValid'>
									Aby przejść do kolejnego kroku kliknij na przycisk <strong>\"Dalej\"</strong>.
								</p>
								<form method='post' class='center' action='".basename($_SERVER['PHP_SELF'])."?aid=".$aid."&amp;step=2'>
								<p><input type='submit' name='check' value='Dalej' /></p>
								</form>
							</div>";
							}
						}
						elseif($_GET['step'] == '2')
						{	
							echo "<div class='tbl2'>";
							echo "<p class='formValid center'>Wykonaj prosze kopię bazy...<p>";
							setcookie("efc_".md5($aid), $aid, time() + $cookies);
							setcookie("efc_lang", $settings['locale'], time() + $cookies);
							setcookie("efc_vers", $settings['ep_version'], time() + $cookies);
							setcookie("efc_core", TRUE, time() + $cookies);
							echo "</div><form method='post' class='center' action='".basename($_SERVER['PHP_SELF'])."?aid=".$aid."&amp;step=3'>
							<p><input type='submit' name='check' value='Dalej' /></p>
							</form>";
						}
						elseif($_GET['step'] == '3')
						{
							$r = $_EFC->stepNum(intval($_GET['step']));
							echo "<div class='tbl2'>";
							echo "<p class='formWarning'>Na tym etapie tworzone są nowe tabele wymagene przez system.<p>";
							
							echo "<div class='tbl2'>Tabela:</div>";
							echo "<ul class='left'>";
							foreach ($r as $value)
							{
								if ($value['status'] === TRUE)
								{
									echo "<li class='Valid'>".$db_prefix.$value['name']." została utworzona.</li>";
								}
								else
								{
									echo "<li class='Warning'>".$db_prefix.$value['name']." nie została utworzona.</li>";
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
							echo "<div class='tbl2'>";
							echo "<p class='formWarning'>Na tym etapie przetwarzane są struktury tabel.<p>";
							
							echo "<div class='tbl2'>Tabela:</div>";
							echo "<ul class='left'>";
							foreach ($r as $value)
							{
								if ($value['status'] === TRUE)
								{
									echo "<li class='Valid'>". $db_prefix.$value['name'] ." została przetworzona.</li>";
								}
								else
								{
									echo "<li class='Warning'>". $db_prefix.$value['name'] ." nie została przetworzona, zgłoś ten błąd!</li>";
								}
							}
							echo "</ul>";
							
							echo "</div><form method='post' class='center' action='".basename($_SERVER['PHP_SELF'])."?aid=".$aid."&amp;step=5'>
							<p><input type='submit' name='check' value='Dalej' /></p>
							</form>";
						}
						elseif($_GET['step'] == '5')
						{
							$r = $_EFC->stepNum(intval($_GET['step']));
							echo "<div class='tbl2'>";
							echo "<p class='formWarning'>Na tym etapie usuwane są stare tabele nie potrzebne już w systemie.<p>";
							
							echo "<div class='tbl2'>Tabela:</div>";
							echo "<ul class='left'>";
							foreach ($r as $value)
							{
								if ($value['status'] === TRUE)
								{
									echo "<li class='Valid'>".$db_prefix."articles</li>";
									echo "<li class='Valid'>".$db_prefix."article_cats</li>";
									echo "<li class='Valid'>".$db_prefix."custom_pages</li>";
									echo "<li class='Valid'>".$db_prefix."downloads</li>";
									echo "<li class='Valid'>".$db_prefix."download_cats</li>";
									echo "<li class='Valid'>".$db_prefix."faqs</li>";
									echo "<li class='Valid'>".$db_prefix."faq_cats</li>";
									echo "<li class='Valid'>".$db_prefix."forums</li>";
									echo "<li class='Valid'>".$db_prefix."forum_attachments</li>";
									echo "<li class='Valid'>".$db_prefix."flood_control</li>";
									echo "<li class='Valid'>".$db_prefix."infusions</li>";
									echo "<li class='Valid'>".$db_prefix."messages_options</li>";
									echo "<li class='Valid'>".$db_prefix."new_users</li>";
									echo "<li class='Valid'>".$db_prefix."photos</li>";
									echo "<li class='Valid'>".$db_prefix."photo_albums</li>";
									echo "<li class='Valid'>".$db_prefix."poll_votes</li>";
									echo "<li class='Valid'>".$db_prefix."polls</li>";
									echo "<li class='Valid'>".$db_prefix."posts</li>";
									echo "<li class='Valid'>".$db_prefix."ratings</li>";
									echo "<li class='Valid'>".$db_prefix."submissions</li>";
									echo "<li class='Valid'>".$db_prefix."vcode</li>";
									echo "<li class='Valid'>".$db_prefix."buttons</li>";
									echo "<li class='Valid'>".$db_prefix."cautions</li>";
									echo "<li class='Valid'>".$db_prefix."cautions_config</li>";
									echo "<li class='Valid'>".$db_prefix."colors</li>";
									echo "<li class='Valid'>".$db_prefix."panels_article</li>";
									echo "<li class='Valid'>".$db_prefix."panels_download</li>";
									echo "<li class='Valid'>".$db_prefix."panels_forum</li>";
									echo "<li class='Valid'>".$db_prefix."rss_builder</li>";
									echo "<li class='Valid'>".$db_prefix."site_links</li>";
									echo "<li class='Valid'>".$db_prefix."site_links_groups</li>";
									echo "<li class='Valid'>".$db_prefix."threads</li>";
									echo "<li class='Valid'>".$db_prefix."thread_notify</li>";
									echo "<li class='Valid'>".$db_prefix."user_groups</li>";
									echo "<li class='Valid'>".$db_prefix."eps_points</li>";
									echo "<li class='Valid'>".$db_prefix."eps_rangs</li>";
									echo "<li class='Valid'>".$db_prefix."forumrang</li>";
									echo "<li class='Valid'>".$db_prefix."weblinks</li>";
									echo "<li class='Valid'>".$db_prefix."weblink_cats</li>";
								}
								else
								{
									echo "<li class='Warning'>".$db_prefix."articles</li>";
									echo "<li class='Warning'>".$db_prefix."article_cats</li>";
									echo "<li class='Warning'>".$db_prefix."captcha</li>";
									echo "<li class='Warning'>".$db_prefix."custom_pages</li>";
									echo "<li class='Warning'>".$db_prefix."downloads</li>";
									echo "<li class='Warning'>".$db_prefix."download_cats</li>";
									echo "<li class='Warning'>".$db_prefix."faqs</li>";
									echo "<li class='Warning'>".$db_prefix."faq_cats</li>";
									echo "<li class='Warning'>".$db_prefix."forums</li>";
									echo "<li class='Warning'>".$db_prefix."forum_attachments</li>";
									echo "<li class='Warning'>".$db_prefix."flood_control</li>";
									echo "<li class='Warning'>".$db_prefix."infusions</li>";
									echo "<li class='Warning'>".$db_prefix."messages_options</li>";
									echo "<li class='Warning'>".$db_prefix."new_users</li>";
									echo "<li class='Warning'>".$db_prefix."photos</li>";
									echo "<li class='Warning'>".$db_prefix."photo_albums</li>";
									echo "<li class='Warning'>".$db_prefix."poll_votes</li>";
									echo "<li class='Warning'>".$db_prefix."polls</li>";
									echo "<li class='Warning'>".$db_prefix."posts</li>";
									echo "<li class='Warning'>".$db_prefix."ratings</li>";
									echo "<li class='Warning'>".$db_prefix."submissions</li>";
									echo "<li class='Warning'>".$db_prefix."vcode</li>";
									echo "<li class='Warning'>".$db_prefix."buttons</li>";
									echo "<li class='Warning'>".$db_prefix."cautions</li>";
									echo "<li class='Warning'>".$db_prefix."cautions_config</li>";
									echo "<li class='Warning'>".$db_prefix."colors</li>";
									echo "<li class='Warning'>".$db_prefix."panels_article</li>";
									echo "<li class='Warning'>".$db_prefix."panels_download</li>";
									echo "<li class='Warning'>".$db_prefix."panels_forum</li>";
									echo "<li class='Warning'>".$db_prefix."rss_builder</li>";
									echo "<li class='Warning'>".$db_prefix."site_links</li>";
									echo "<li class='Warning'>".$db_prefix."site_links_groups</li>";
									echo "<li class='Warning'>".$db_prefix."threads</li>";
									echo "<li class='Warning'>".$db_prefix."thread_notify</li>";
									echo "<li class='Warning'>".$db_prefix."user_groups</li>";
									echo "<li class='Warning'>".$db_prefix."eps_points</li>";
									echo "<li class='Warning'>".$db_prefix."eps_rangs</li>";
									echo "<li class='Warning'>".$db_prefix."forumrang</li>";
									echo "<li class='Warning'>".$db_prefix."weblinks</li>";
									echo "<li class='Warning'>".$db_prefix."weblink_cats</li>";
								}
							}
							echo "</ul>";
							echo "</div><form method='post' class='center' action='".basename($_SERVER['PHP_SELF'])."?aid=".$aid."&amp;step=6'>
							<p><input type='submit' name='check' value='Dalej' /></p>
							</form>";
						}
						elseif($_GET['step'] == '6')
						{
							$r = $_EFC->stepNum(intval($_GET['step']));
							echo "<div class='tbl2'>";
							echo "<p class='formWarning'>Na tym etapie przetwarzana jest reszta tabel.<p>";
							
							echo "<div class='tbl2'>Tabela:</div>";
							echo "<ul class='left'>";
							foreach ($r as $value)
							{
								if ($value['status'] === TRUE)
								{
									echo "<li class='Valid'>". $db_prefix.$value['name'] ." została przetworzona.</li>";
								}
								else
								{
									echo "<li class='Warning'>". $db_prefix.$value['name'] ." nie została przetworzona.</li>";
								}
							}
							echo "</ul>";
							echo "</div><form method='post' class='center' action='".basename($_SERVER['PHP_SELF'])."?aid=".$aid."&amp;step=7'>
							<p><input type='submit' name='check' value='Dalej' /></p>
							</form>";
						}
						elseif($_GET['step'] == '7')
						{
							$r = $_EFC->stepNum(intval($_GET['step']));
							echo "<div class='tbl2'>";
							echo "<p class='formWarning'>Na tym etapie przetwarzana jest reszta tabel.<p>";
							
							echo "<div class='tbl2'>Tabela:</div>";
							echo "<ul class='left'>";
							foreach ($r as $value)
							{
								if ($value['status'] === TRUE)
								{
									echo "<li class='Valid'>". $db_prefix.$value['name'] ." została przetworzona.</li>";
								}
								else
								{
									echo "<li class='Warning'>". $db_prefix.$value['name'] ." nie została przetworzona.</li>";
								}
							}
							echo "</ul>";
							echo "</div><form method='post' class='center' action='".basename($_SERVER['PHP_SELF'])."?aid=".$aid."&amp;step=8'>
							<p><input type='submit' name='check' value='Dalej' /></p>
							</form>";
						}
						elseif($_GET['step'] == '8')
						{
							$r = $_EFC->stepNum(intval($_GET['step']));
							echo "<div class='tbl2'>";
							echo "<p class='formWarning'>Na tym etapie przetwarzana jest reszta tabel.<p>";
							
							echo "<div class='tbl2'>Tabela:</div>";
							echo "<ul class='left'>";
							foreach ($r as $value)
							{
								if ($value['status'] === TRUE)
								{
									echo "<li class='Valid'>". $db_prefix.$value['name'] ." została przetworzona.</li>";
								}
								else
								{
									echo "<li class='Warning'>". $db_prefix.$value['name'] ." nie została przetworzona.</li>";
								}
							}
							echo "</ul>";
							echo "</div><form method='post' class='center' action='".basename($_SERVER['PHP_SELF'])."?aid=".$aid."&amp;step=9'>
							<p><input type='submit' name='check' value='Dalej' /></p>
							</form>";
						}
						elseif($_GET['step'] == '9')
						{
							$r = $_EFC->stepNum(intval($_GET['step']));
							echo "<div class='tbl2'>";
							echo "<p class='formWarning'>Na tym etapie przetwarzana jest reszta tabel.<p>";
							
							echo "<div class='tbl2'>Tabela:</div>";
							echo "<ul class='left'>";
							foreach ($r as $value)
							{
								if ($value['status'] === TRUE)
								{
									echo "<li class='Valid'>". $db_prefix.$value['name'] ." została przetworzona.</li>";
								}
								else
								{
									echo "<li class='Warning'>". $db_prefix.$value['name'] ." nie została przetworzona.</li>";
								}
							}
							echo "</ul>";
							echo "</div><form method='post' class='center' action='".basename($_SERVER['PHP_SELF'])."?aid=".$aid."&amp;step=10'>
							<p><input type='submit' name='check' value='Dalej' /></p>
							</form>";
						}
						elseif($_GET['step'] == '10')
						{
							echo "<p class='formValid'>Sukces!<br />";
							echo "Twój system został zaktualizowany do najnowszej wersji systemu eXtreme-Fusion.<br />";
							echo "Teraz możesz usunąć stare pliki z eXtreme-Fusion i wrzuciś pliki z nowego systemu eXtreme-Fusion.<br />";
							echo "Wyedytuj plik config.php zamieniając jego zawartość na:<br /></p>";
							echo "<div class='center'>".$_EFC->stepNum(intval($_GET['step']))."</div><br />";
							echo "<p class='formValid'><a href='../../index.php' class='green bold'>Gdy skończysz wrzucać pliki na swój serwer oraz dokonasz edycji pliku config.php będziesz mógł zobaczyć swoją nową stronę klikając na ten napis.</a></p>";
							
						}
					}
mysql_close();
ob_end_flush();			
echo '
				</div>
				<div class="clear"></div>

			</div>
			<hr />
			<div class="tab-click" id="crew-list"><a href="javascript:void(0)">Developers of eXtreme-Fusion v5.0</a></div>
			<div id="tab-crew-list" class="tab-cont">
				<div class="center">
					<div id="leaders">
						<div class="left"><span class="bold">Project founder:</span> Wojciech (zer0) Mycka</div>
						<div class="right"><span class="bold">Project leader:</span> Paweł (Inscure) Zegardło</div>
					</div>
					<div class="clear"></div>

					<div id="team">
						<div class="bold">Code Developers:</div>

						<p>Andrzej (Andrzejster) Sternal</p>
						<p>Dominik (Domon) Barylski</p>
						<p>Paweł (Inscure) Zegardło</p>
						<p>Piotr (piotrex41) Krzysztofik</p>
						<p>Rafał (Rafik89) Krupiński</p>
						<p>Wojciech (zer0) Mycka</p>

						<div class="bold">Design Developers:</div>

						<p>Andrzej (Andrzejster) Sternal </p>
						<p>Piotr (piotrex41) Krzysztofik</p>
						<p>Wojciech (zer0) Mycka </p>

						<div class="bold">Language Team:</div>

						<p>Marcin (Tymcio) Tymków - English language files</p>
						<p>Pavel (LynX) Laurenčík - Czech language files</p>

						<div class="bold">jQuery & Ajax Developers:</div>

						<p>Dominik (Domon) Barylski</p>
						<p>Paweł (Inscure) Zegardło </p>
						<p>Wojciech (zer0) Mycka </p>

						<div class="bold">Beta testers:</div>

						<p>Dariusz (Chomik) Markiewicz</p>
						<p>Mariusz (FoxNET) Bartoszewicz</p>

					</div>
				</div>
			</div>
			<div class="center" >
				<p>Copyright © 2005 - 2013 by the <a href="http://extreme-fusion.org/" rel="copyright">eXtreme-Fusion</a> Crew</p>
				<p>Copyright 2002-2013 <a href="http://php-fusion.co.uk/">PHP-Fusion</a>. Released as free software without warranties under <a href="http://www.fsf.org/licensing/licenses/agpl-3.0.html">aGPL v3</a>.</p>
			</div>
			<hr />
			<div class="right" style="font-size:xx-small;text-align:right;">';
				$time_gen = $_EFC->getTime() - $start_gen;
				if($time_gen >= 1) 
				{
					$time_gen = round($time_gen, 5).' sekund';
				} 
				elseif($time_gen < 1) 
				{
					$time_gen = (round($time_gen, 5)*1000).' milisekund ('.round($time_gen, 3).'s)';
				}
				echo 'Czas generowania strony: '.$time_gen.' <br />
				Użycie pamięci: '.(memory_get_peak_usage(TRUE)/1048576).' Mb<br />
				Ilość zapytań: '.$_EFC->getSQLQueries().'
			</div>
			<div class="clear" ></div>
		</div>
		<div class="cfl"><div class="cfr"><div class="cfc"></div></div></div></div>
	</body>
</html>';
?>