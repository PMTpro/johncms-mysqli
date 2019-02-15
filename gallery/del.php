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
    $typ = $db->query("select * from `gallery` where id='" . $id . "';");
    $ms = $typ->fetch_array();
    if (isset($_GET['yes'])) {
        switch ($ms['type']) {
            case "al":
                $ft = $db->query("select * from `gallery` where `type`='ft' and `refid`='" . $id . "';");
                while ($ft1 = $ft->fetch_array()) {
                    $km = $db->query("select * from `gallery` where type='km' and refid='" . $ft1['id'] . "';");
                    while ($km1 = $km->fetch_array()) {
                        $db->query("delete from `gallery` where `id`='" . $km1['id'] . "';");
                    }
                    unlink("foto/$ft1[name]");
                    $db->query("delete from `gallery` where `id`='" . $ft1['id'] . "';");
                }
                $db->query("delete from `gallery` where `id`='" . $id . "';");
                header("location: index.php?id=$ms[refid]");
                break;

            case "rz":
                $al = $db->query("select * from `gallery` where type='al' and refid='" . $id . "';");
                while ($al1 = $al->fetch_array()) {
                    $ft = $db->query("select * from `gallery` where type='ft' and refid='" . $al1['id'] . "';");
                    while ($ft1 = $ft->fetch_array()) {
                        $km = $db->query("select * from `gallery` where type='km' and refid='" . $ft1['id'] . "';");
                        while ($km1 = $km->fetch_array()) {
                            $db->query("delete from `gallery` where `id`='" . $km1['id'] . "';");
                        }
                        unlink("foto/$ft1[name]");
                        $db->query("delete from `gallery` where `id`='" . $ft1['id'] . "';");
                    }
                    $db->query("delete from `gallery` where `id`='" . $al1['id'] . "';");
                }
                $db->query("delete from `gallery` where `id`='" . $id . "';");
                header("location: index.php");
                break;

            default:
                echo "ERROR<br/><a href='index.php'>Back</a><br/>";
                require_once('../incfiles/end.php');
                exit;
                break;
        }
    } else {
        switch ($ms['type']) {
            case "al":
                echo $lng['delete_confirmation'] . " $ms[text]?<br/>";
                break;

            case "rz":
                echo $lng['delete_confirmation'] . " $ms[text]?<br/>";
                break;

            default:
                echo "ERROR<br/><a href='index.php'>" . $lng['to_gallery'] . "</a><br/>";
                require_once('../incfiles/end.php');
                exit;
                break;
        }
        echo "<a href='index.php?act=del&amp;id=" . $id . "&amp;yes'>" . $lng['delete'] . "</a> | <a href='index.php?id=" . $id . "'>" . $lng['cancel'] . "</a><br/>";
    }
} else {
    header("location: index.php");
}

?>