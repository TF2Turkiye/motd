<?php

if(!defined("IN_MYBB"))
{
	die("Direct initialization of this file is not allowed.");
}
$codename = str_replace('.php', '', basename(__FILE__));

$plugins->add_hook('misc_start', 'sunucular_index');
$plugins->add_hook('fetch_wol_activity_end', 'motd_fetch_wol_activity_end');
$plugins->add_hook('build_friendly_wol_location_end', 'motd_build_friendly_wol_location_end');

function sunucular_info()
{
	return array(
		"name"			=> "[TF2 Turkiye] Sunucular",
		"description"	=> "Sunucu listesini gosteren eklenti.",
		"website"		=> "https://tf2turkiye.com",
		"author"		=> "Kerem",
		"authorsite"	=> "https://kkerem.com",
		"version"		=> "1.0",
		"guid" 			=> "",
		"codename"		=> $codename,
		"compatibility" => "*"
	);
}
function cezalistesi_is_installed()
{
    global $db;
    if($db->table_exists("sunucular"))
    {
        return true;
    }
    return false;
}
function cezalistesi_install()
{
    $templates = [
        'sunucular_bilgi' => '<div class="row vertical-align mb-1 pt-2 px-2 mx-0 text-muted fz-11">
        <div class="col-12 col-md-4 mx-0 px-0 pl-md-2 pl-md-3 mr-md-2 text-center text-md-left">
            Toplam <span class="text-body">{$oku[\'sunucusayisi\']}</span> {$lang->sunucusayisi}
            <span class="text-body">{$oku[\'yetkilisayisi\']}</span> {$lang->yetkilisayisi}
        </div>
        <div class="col-6 col-md-3 fz-11 text-md-right pl-2 pl-md-0 pr-0 ml-md-1">
            <span class="text-body"></span> {$lang->aylik_oyuncusayisi}
        </div>
        <div class="col-6 col-md-2 text-right pl-0 pr-2 px-md-2 fz-11 my-2 my-md-0">
            <span class="text-body"></span> {$lang->kayitli_oyuncusayisi}
        </div>
        <div class="col-12 col-md-2 px-0 ml-0 text-center text-md-right">
            <span class="text-body">{$sunucular}</span> {$lang->aktifoyuncu}
        </div>
    </div>',
        'sunucular_index' => '<html>
        <head>
                <meta name="description" content="Sunucularımızın anlık durumunu buradan görüntüleyebilirsiniz." />
        <title>{$lang->bildirim}{$lang->sunucular} :: {$mybb->settings[\'bbname\']}</title>
        {$headerinclude}
        </head>
        <body<?php echo dark($mybb->user[\'darkmod\']); ?>>
        {$header}<div class="row vertical-align bg-brown rounded-top p-2 mx-0 shadow-sm">
            <div class="container bg-saydam rounded p-2 shadow-sm">
                <div class="col-12 text-left text-white px-0">
                    <i class="fas fa-server ml-1 mr-2"></i><h1 class="nostyle m-0 p-0 d-inline-block">{$lang->sunucular}</h1>
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
        </html>',
        'sunucular_row' => '<div class="container bg-white opacity rounded shadow-sm border-bottom-lastchild">
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
                <a href="{$baglan}"><i class="fas fa-sign-in-alt mr-2"></i>{$lang->oyunakatil}</a>
            </div>
    
    
        </div>
    </div>',
        'motd' => '<html>
        <head>
            <title>{$lang->bildirim}MOTD :: {$mybb->settings[\'bbname\']} </title>
            {$headerinclude}
            <style type="text/css">
                @font-face {
                    font-family: \'TF2 Build\';
                    src: url(\'images/TF2Build.woff2\') format(\'woff2\'),
                        url(\'images/TF2Build.woff\') format(\'woff\'),
                        url(\'images/TF2Build.ttf\') format(\'truetype\'),
                        url(\'images/TF2Build.svg#TF2Build\') format(\'svg\');
                    font-weight: normal;
                    font-style: normal;
                }
                body {
                    -webkit-touch-callout: none; 
        -webkit-user-select: none;
         -khtml-user-select: none;
           -moz-user-select: none;
            -ms-user-select: none; 
                user-select: none; 
                }
                .sunucular {
                    font-family: \'TF2 Build\';
                    font-weight: normal;
                    font-style: normal;
                    letter-spacing: .02rem;
                }
            </style>
            <script type="text/javascript">
                jQuery(function() {
                    var quotes = jQuery(".quotes");
                    var quoteIndex = -1;
                    function showNextQuote() {
                        ++quoteIndex;
                        quotes.eq(quoteIndex % quotes.length)
                            .fadeIn(500)
                            .delay(2500)
                            .fadeOut(500, showNextQuote);
                    }
                    showNextQuote();
                });
            </script>
        </head>
        <body<?php echo dark($mybb->user[\'darkmod\']); ?>>
        <img src="{$mybb->settings[\'bburl\']}/images/motd_bg2.png" id="bg" style="position:absolute;top:0;left:0;width:100%;height:100%;z-index:-1;">
        <div class="container-fluid">
    
            <div class="mt-4 mt-md-5 mb-4 text-center">
                <img src="{$mybb->settings[\'bburl\']}/images/logo_normal.png" class="my-2">
            </div>
    
            <div class="bg-white rounded shadow-sm p-2 my-4">
                <div class="p-2">
                    <nav class="navbar navbar-expand-lg navbar-light p-0 m-0">
                        <div class="row w-100">
                            <div class="col-auto pr-0">
                                <i class="fas fa-bullhorn mr-2 fz-16 text-muted"></i>
                            </div>
                            <div class="col px-1">
                                <ul class="navbar-nav nav w-100">
                                    {$motd_mesajlar}
                                </ul>
                            </div>
                        </div>
                        </div>
                    </nav>
            </div>
    
            <div class="row text-white sunucular my-4">
                {$motd_row}
            </div>
    
            <div class="p-2 my-2 text-center" style="color: rgba(255,255,255,.8);">
                Sunucularımız arasında <span class="bg-orange text-white p-1 rounded mx-2">!sunucular</span> yazarak geçiş yapabilirsiniz!
            </div>
    
        </div>
        </body>
    </html>',
        'motd_bilgi' => '<div class="row vertical-align mb-1 pt-2 px-2 mx-0 text-muted fz-11">
        <div class="col-12 col-md-4 mx-0 px-0 pl-md-2 pl-md-3 mr-md-2 text-center text-md-left">
            Toplam <span class="text-body">{$oku[\'sunucusayisi\']}</span> {$lang->sunucusayisi}
            <span class="text-body">{$oku[\'yetkilisayisi\']}</span> {$lang->yetkilisayisi}
        </div>
        <div class="col-6 col-md-3 fz-11 text-md-right pl-2 pl-md-0 pr-0 ml-md-1">
            <span class="text-body"></span> {$lang->aylik_oyuncusayisi}
        </div>
        <div class="col-6 col-md-2 text-right pl-0 pr-2 px-md-2 fz-11 my-2 my-md-0">
            <span class="text-body"></span> {$lang->kayitli_oyuncusayisi}
        </div>
        <div class="col-12 col-md-2 px-0 ml-0 text-center text-md-right">
            <span class="text-body">{$sunucular}</span> {$lang->aktifoyuncu}
        </div>
    </div>',
        'motd_mesajlar' => '<li class="quotes nav-item active mx-1" style="display: none;">{$row[\'mesaj\']}</li>',
        'motd_row' => '<div class="col-6 fz-14">
        <div class="row vertical-align mb-1 py-2">
            <div class="col-10 pl-2 pl-md-3" style="color: rgba(255,255,255,.8);">
                {$sunucuadi}
            </div>
            <div class="col-2 pl-0 pr-3 text-right">
                <span class="font-weight-bold">{$oyuncu}</span> <sup>/{$maxoyuncu}</sup>
            </div>
        </div>
    </div>',
    ];

    $data = [];

    foreach ($templates as $name => $content) {
        $data[] = [
            'title'    => $name,
            'template' => $db->escape_string($content),
            'sid'      => -1,
            'version'  => 1,
            'status'   => '',
            'dateline' => TIME_NOW,
        ];
    }

    $db->insert_query_multiple('templates', $data);
}

function sunucular_index() 
{
    global $db, $lang, $mybb, $theme, $templates, $header, $footer, $headerinclude, $sunucular, $sunucular_bilgi, $motd_bilgi;

    if($mybb->get_input('action') == 'sunucular')
    {
        add_breadcrumb($lang->sunucular, "misc.php?action=sunucular");
        

        $query = $db->query("SELECT * FROM mybb_sunucular ORDER BY id ASC");
        while($s = $db->fetch_array($query))
        {
            $name = explode(' [',str_replace(':: ', ':: <span class="text-body">', $s['name']));
            $sunucuadi = $name[0];
            $harita = $s['map'];
            $ip = $s['address'];
            $oyuncu = $s['players'];
            $maxoyuncu = $s['maxplayers'];
            $baglan = 'steam://connect/' . $s['address'];
            eval("\$sunucular_row .= \"".$templates->get("sunucular_row")."\";");
        }
        
        // $sunucusayisi = $db->fetch_array($db->query("SELECT SUM(players) AS players FROM mybb_sunucular"));

        eval('$page  = "' . $templates->get('sunucular_index') . '";');

        output_page($page);
    }

    if($mybb->get_input('action') == 'motd')
    {
        
        function motd_fetch_wol_activity_end(&$args)
        {
            $args['activity'] = 'motd';
        }
        function motd_build_friendly_wol_location_end(&$args)
        {
            global $awards, $lang, $settings;
            $lang->load("sunucular");

            if($args['user_activity']['activity'] == 'motd')
            {
                $args['location_name'] = $lang->sprintf($lang->sunucular_wol);
            }
        }

        // header("Location: https://tf2turkiye.com/misc.php?action=steam_login");
        add_breadcrumb("MOTD", "misc.php?action=motd");
        

        $query = $db->query("SELECT * FROM mybb_sunucular ORDER BY id ASC");
        while($s = $db->fetch_array($query))
        {
            $name = explode(' [',str_replace(':: ', ':: <span class="font-weight-bold text-white">', $s['name']));
            $sunucuadi = $name[0];
            $harita = $s['map'];
            $ip = $s['address'];
            $oyuncu = $s['players'];
            $maxoyuncu = $s['maxplayers'];
            $baglan = 'steam://connect/' . $s['address'];
            eval("\$motd_row .= \"".$templates->get("motd_row")."\";");
        }
        $query = $db->query("SELECT * FROM mybb_motd ORDER BY id ASC");
        while($row = $db->fetch_array($query))
        {
            eval("\$motd_mesajlar.= \"".$templates->get("motd_mesajlar")."\";");
        }
        
        // $sunucusayisi = $db->fetch_array($db->query("SELECT SUM(players) AS players FROM mybb_sunucular"));

        eval('$motd_bilgi  = "' . $templates->get('motd_bilgi') . '";');
        eval('$page  = "' . $templates->get('motd') . '";');

        output_page($page);
    }
}
