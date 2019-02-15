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
if ($rights == 3 || $rights >= 6) {
    if (!$id) {
        require('../incfiles/head.php');
        echo functions::display_error($lng['error_wrong_data']);
        require('../incfiles/end.php');
        exit;
    }
    // Проверяем, существует ли тема
    $req = $db->query("SELECT * FROM `forum` WHERE `id` = '$id' AND `type` = 't'");
    if (!$req->num_rows) {
        require('../incfiles/head.php');
        echo functions::display_error($lng_forum['error_topic_deleted']);
        require('../incfiles/end.php');
        exit;
    }
    $res = $req->fetch_assoc();
    if (isset($_POST['submit'])) {
        $del = isset($_POST['del']) ? intval($_POST['del']) : NULL;
        if ($del == 2 && core::$user_rights == 9) {
            /*
            -----------------------------------------------------------------
            Удаляем топик
            -----------------------------------------------------------------
            */
            $req1 = $db->query("SELECT * FROM `cms_forum_files` WHERE `topic` = '$id'");
            if ($req1->num_rows) {
                while ($res1 = $req1->fetch_assoc()) {
                    unlink('../files/forum/attach/' . $res1['filename']);
                }
                $db->query("DELETE FROM `cms_forum_files` WHERE `topic` = '$id'");
                $db->query("OPTIMIZE TABLE `cms_forum_files`");
            }
            $db->query("DELETE FROM `forum` WHERE `refid` = '$id'");
            $db->query("DELETE FROM `forum` WHERE `id`='$id'");
        } elseif ($del = 1) {
            /*
            -----------------------------------------------------------------
            Скрываем топик
            -----------------------------------------------------------------
            */
            $db->query("UPDATE `forum` SET `close` = '1', `close_who` = '$login' WHERE `id` = '$id'");
            $db->query("UPDATE `cms_forum_files` SET `del` = '1' WHERE `topic` = '$id'");
        }
        header('Location: index.php?id=' . $res['refid']);
    } else {
        /*
        -----------------------------------------------------------------
        Меню выбора режима удаления темы
        -----------------------------------------------------------------
        */
        require('../incfiles/head.php');
        echo '<div class="phdr"><a href="index.php?id=' . $id . '"><b>' . $lng['forum'] . '</b></a> | ' . $lng_forum['topic_delete'] . '</div>' .
             '<div class="rmenu"><form method="post" action="index.php?act=deltema&amp;id=' . $id . '">' .
             '<p><h3>' . $lng['delete_confirmation'] . '</h3>' .
             '<input type="radio" value="1" name="del" checked="checked"/>&#160;' . $lng['hide'] . '<br />' .
             (core::$user_rights == 9 ? '<input type="radio" value="2" name="del" />&#160;' . $lng['delete'] . '</p>' : '') .
             '<p><input type="submit" name="submit" value="' . $lng['do'] . '" /></p>' .
             '<p><a href="index.php?id=' . $id . '">' . $lng['cancel'] . '</a>' .
             '</p></form></div>' .
             '<div class="phdr">&#160;</div>';
    }
} else {
    echo functions::display_error($lng['access_forbidden']);
}