<?php

if (!defined("IN_MYBB"))
	{
	die("Direct initialization of this file is not allowed.");
	}

$codename = str_replace('.php', '', basename(__FILE__));
$plugins->add_hook('misc_start', 'sunucular_index');

function sunucular_info()
	{
	return array(
		"name" => "[TF2 Turkiye] Sunucular",
		"description" => "Sunucu listesini gosteren eklenti.",
		"website" => "https://tf2turkiye.com",
		"author" => "Kerem",
		"authorsite" => "https://kkerem.com",
		"version" => "1.0",
		"guid" => "",
		"codename" => $codename,
		"compatibility" => "*"
	);
	}

function myplugin_is_installed()
	{
	global $db;
	if ($db->table_exists("sunucular"))
		{
		return true;
		}

	return false;
	}

function myplugin_install()
	{
	global $db, $mybb;
	$setting_group = array(
		'name' => 'sunucularlistesi',
		'title' => 'TF2 Turkiye :: Sunucular',
		'description' => 'Sunucuları gösteren sayfa.',
		'disporder' => 5,
		'isdefault' => 0
	);
	$gid = $db->insert_query("settinggroups", $setting_group);
	$setting_array = array(
		's_aktiflik' => array(
			'title' => 'Sunucular sayfasi aktifmi?',
			'description' => 'Sunucular kontrol',
			'optionscode' => 'yesno',
			'value' => 1,
			'disporder' => 1
		) ,
	);
	foreach($setting_array as $name => $setting)
		{
		$setting['name'] = $name;
		$setting['gid'] = $gid;
		$db->insert_query('settings', $setting);
		}

	rebuild_settings();
	$template = '
    <div class="row vertical-align mb-1 pt-2 px-2 mx-0 text-muted fz-11">
	<div class="col-12 col-md-4 mx-0 px-0 pl-md-2 pl-md-3 mr-md-2 text-center text-md-left">
		Toplam <span class="text-body">{$oku['sunucusayisi']}</span> {$lang->sunucusayisi}
		<span class="text-body">{$oku['yetkilisayisi']}</span> {$lang->yetkilisayisi}
	</div>
	<div class="col-6 col-md-3 fz-11 text-md-right pl-2 pl-md-0 pr-0 ml-md-1">
		<span class="text-body"><?php
$sayi1        = "{$oku['aylik_oyuncusayisi']}";
$sayikontrol = str_replace(', ', '', $sayi1);

 if ($sayikontrol > 999) {
      echo number_format(str_replace(' . ', ' . ', $sayi1), 0) . $lang->birim;
  }
    else {
      echo $sayi1;
  }
?></span> {$lang->aylik_oyuncusayisi}
	</div>
	<div class="col-6 col-md-2 text-right pl-0 pr-2 px-md-2 fz-11 my-2 my-md-0">
		<span class="text-body"><?php
$sayi1        = "{$oku['kayitli_oyuncusayisi']}";
$sayikontrol = str_replace(', ', '', $sayi1);

 if ($sayikontrol > 999) {
      echo number_format(str_replace(' . ', ' . ', $sayi1), 0) . $lang->birim;
  }
    else {
      echo $sayi1;
  }
?></span> {$lang->kayitli_oyuncusayisi}
	</div>
	<div class="col-12 col-md-2 px-0 ml-0 text-center text-md-right">
		<span class="text-body">{$sunucular}</span> {$lang->aktifoyuncu}
	</div>
</div>
';
	$insert_array = array(
		'title' => 'sunucular_bilgi',
		'template' => $db->escape_string($template) ,
		'sid' => '-1',
		'version' => '',
		'dateline' => time()
	);
	$db->insert_query('templates', $insert_array);
	$template = '
    <html>
<head>
<title>{$lang->sunucular} :: {$mybb->settings['bbname']}</title>
{$headerinclude}
</head>
<body>
{$header}<div class="row vertical-align bg-brown rounded-top p-2 mx-0 shadow-sm">
	<div class="container bg-saydam rounded p-2 shadow-sm">
		<div class="col-12 text-left text-white px-0">
			<i class="fas fa-server ml-1 mr-2"></i>{$lang->sunucular}
		</div>
	</div>
</div>
<div class="bg-light rounded-bottom px-2 py-1 shadow-sm mb-2">
	{$sunucular_row}
</div>

	{$sunucular_bilgi}
</div>
	{$footer}
	</body>
</html>';
	$insert_array = array(
		'title' => 'sunucular_index',
		'template' => $db->escape_string($template) ,
		'sid' => '-1',
		'version' => '',
		'dateline' => time()
	);
	$db->insert_query('templates', $insert_array);
	$template = '
    <div class="container bg-white opacity rounded shadow-sm border-bottom-lastchild">
	<div class="row vertical-align mb-1 py-2 pl-2 pr-0 border-bottom text-muted">
		<div class="col-auto pl-1 position-relative d-none d-md-block">
			<i class="fas fa-arrow-up position-absolute fz-10 text-success fa-pull-right pl-2"></i>
			<i class="fas fa-globe-americas text-muted"></i>
		</div>
		<div class="col-10 col-md-4 pl-2 pl-md-3">
			<i class="fas fa-server mr-2 d-inline-block d-md-none"></i>{$sunucuadi}
		</div>
		<div class="col-3 d-none d-md-block fz-11 text-right px-0 pr-4">
			{$harita}
		</div>
		<div class="col-2 d-none d-md-block text-right pl-0 pr-4 fz-11 js-tooltip js-copy cursor-pointer" data-toggle="tooltip" data-placement="bottom" data-copy="{$ip}" data-original-title="Kopyalamak için tıkla" title="">
			{$ip}
		</div>
		<div class="col-2 col-md-1 pr-3 pr-md-0 pl-md-3 pl-0 text-right">
			<span class="text-info">{$oyuncu}</span> <sup>/{$maxoyuncu}</sup>
		</div>
		<div class="col-auto d-none d-md-block text-right pl-4 pr-0">
			<a href="{$baglan}"><i class="fas fa-sign-in-alt mr-2"></i>Oyuna Katil</a>
		</div>


	</div>
</div>';
	$insert_array = array(
		'title' => 'sunucular_row',
		'template' => $db->escape_string($template) ,
		'sid' => '-1',
		'version' => '',
		'dateline' => time()
	);
	$db->insert_query('templates', $insert_array);
	}

function sunucular_index()
	{
	global $db, $lang, $mybb, $theme, $templates, $header, $footer, $headerinclude, $sunucular, $sunucular_bilgi;
	if ($mybb->get_input('action') == 'sunucular')
		{
		add_breadcrumb($lang->sunucular, "misc.php?action=sunucular");
		$query = $db->query("SELECT * FROM mybb_sunucular ORDER BY id ASC");
		while ($s = $db->fetch_array($query))
			{
			$name = explode(' [', str_replace(':: ', ':: <span class="text-body">', $s['name']));
			$sunucuadi = $name[0];
			$harita = $s['map'];
			$ip = $s['address'];
			$oyuncu = $s['players'];
			$maxoyuncu = $s['maxplayers'];
			$baglan = 'steam://connect/' . $s['address'];
			eval("\$sunucular_row .= \"" . $templates->get("sunucular_row") . "\";");
			}

		// $sunucusayisi = $db->fetch_array($db->query("SELECT SUM(players) AS players FROM mybb_sunucular"));

		eval('$page  = "' . $templates->get('sunucular_index') . '";');
		output_page($page);
		}
	}
