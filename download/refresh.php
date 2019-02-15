<?php

/**
* @package     JohnCMS
* @link        http://johncms.com
* @copyright   Copyright (C) 2008-2011 JohnCMS Community
* @license     LICENSE.txt (see attached file)
* @version     VERSION.txt (see attached file)
* @author      http://johncms.com/about
*/

defined('_IN_JOHNCMS') or die('Error: restricted access');
require_once("../incfiles/head.php");
if ($rights == 4 || $rights >= 6) {
    $drt = array ();
    $dropen = opendir("$loadroot");
    while (($file1 = readdir($dropen))) {
        if ($file1 != "." && $file1 != ".." && $file1 != "index.php") {
            $ob = $db->query("select * from `download` where type = 'cat' and refid = ''  ;");
            while ($ob1 = $ob->fetch_array()) {
                $drt[] = $ob1[name];
            }
            if (!in_array($file1, $drt)) {
                if (is_dir("$loadroot/$file1")) {
                    $db->query("insert into `download` values(0,'','" . $loadroot . "','" . time() . "','" . $file1 . "','cat','','','','" . $file1 . "','');");
                }
            }
        }
    }
    $obn = $db->query("select * from `download` where type = 'cat' ;");
    while ($obn1 = $obn->fetch_array()) {
        $dirop = "$obn1[adres]/$obn1[name]";
        if (is_dir("$dirop")) {
            $diropen = opendir("$dirop");
            while (($file = readdir($diropen))) {
                if ($file != "." && $file != ".." && $file != "index.php") {
                    $pap = "$obn1[adres]/$obn1[name]";
                    $obn2 = $db->query("select * from `download` where name = '" . functions::check($file) . "' and adres = '" . $pap . "' ;");
                    while ($obndir = $obn2->fetch_array()) {
                        $fod[] = $obndir[name];
                    }
                    if (!in_array($file, $fod)) {
                        if (is_dir("$dirop/$file")) {
                            $db->query("insert into `download` values(0,'" . $obn1[id] . "','" . $pap . "','" . time() . "','" . $file . "','cat','','','','" . $file . "','');");
                        }
                        if (is_file("$dirop/$file")) {
                            $db->query("insert into `download` values(0,'" . $obn1[id] . "','" . $pap . "','" . time() . "','" . $file . "','file','','','','','');");
                        }
                    }
                    $fod = array (); ########## 7.02.08
                }
            }
        }
    }
    $dres = $db->query("select * from `download` where type = 'cat' and time = '" . time() . "' ;");
    $totald = $dres->num_rows;
    $fres = $db->query("select * from `download` where type = 'file' and time = '" . time() . "' ;");
    $totalf = $fres->num_rows;
    $deld = $db->query("select * from `download` where type = 'cat' ;");
    $idd = 0;
    while ($deld1 = $deld->fetch_array()) {
        if (!is_dir("$deld1[adres]/$deld1[name]")) {
            $db->query("delete from `download` where id='" . $deld1[id] . "' LIMIT 1;");
            $idd = $idd + 1;
        }
    }
    $delf = $db->query("select * from `download` where type = 'file' ;");
    $idf = 0;
    while ($delf1 = $delf->fetch_array()) {
        if (!is_file("$delf1[adres]/$delf1[name]")) {
            $db->query("delete from `download` where id='" . $delf1[id] . "' LIMIT 1;");
            $idf = $idf + 1;
        }
    }
    echo '<h3>' . $lng_dl['refreshed'] . "</h3>" . $lng_dl['added'] . " $totald " . $lng_dl['folders'] . " и $totalf " . $lng_dl['files'] . "<br/>
" . $lng_dl['deleted'] . " $idd " . $lng_dl['folders'] . " и $idf " . $lng_dl['files'] . "<br/>";
    if ($totald != 0 || $totalf != 0) {
        echo "<a href='?act=refresh'>" . $lng_dl['refresh_continue'] . "</a><br/>";
    }
}
echo "<p><a href='?'>" . $lng['back'] . "</a></p>";
?>