<?php

/*
////////////////////////////////////////////////////////////////////////////////
// JohnCMS                Mobile Content Management System                    //
// Project site:          http://johncms.com                                  //
// Support site:          http://gazenwagen.com                               //
////////////////////////////////////////////////////////////////////////////////
// Lead Developer:        Oleg Kasyanov   (AlkatraZ)  alkatraz@gazenwagen.com //
// Development Team:      Eugene Ryabinin (john77)    john77@gazenwagen.com   //
//                        Dmitry Liseenko (FlySelf)   flyself@johncms.com     //
////////////////////////////////////////////////////////////////////////////////
*/

defined('_IN_JOHNCMS') or die('Error: restricted access');

if ($rights >= 6) {
    if ($_GET['id'] == "") {
        echo "ERROR<br/><a href='index.php'>Back</a><br/>";
        require_once('../incfiles/end.php');
        exit;
    }
    $id = intval($_GET['id']);
    $typ = $db->query("select * from `gallery` where id='" . $id . "';");
    $ms = $typ->fetch_array();
    if ($ms['type'] != "ft") {
        echo "ERROR<br/><a href='index.php'>Back</a><br/>";
        require_once('../incfiles/end.php');
        exit;
    }
    if (isset($_GET['yes'])) {
        $km = $db->query("select * from `gallery` where type='km' and refid='" . $id . "';");
        while ($km1 = $km->fetch_array()) {
            $db->query("delete from `gallery` where `id`='" . $km1['id'] . "';");
        }
        unlink("foto/$ms[name]");
        $db->query("delete from `gallery` where `id`='" . $id . "';");
        header("location: index.php?id=$ms[refid]");
    } else {
        echo $lng['delete_confirmation'] . "<br/>";
        echo "<a href='index.php?act=delf&amp;id=" . $id . "&amp;yes'>" . $lng['delete'] . "</a> | <a href='index.php?id=" . $ms['refid'] . "'>" . $lng['cancel'] . "</a><br/>";
    }
}

?>