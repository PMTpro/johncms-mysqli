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

/*
-----------------------------------------------------------------
История активности
-----------------------------------------------------------------
*/
$textl = htmlspecialchars($user['name']) . ': ' . $lng_profile['activity'];
require('../incfiles/head.php');
echo '<div class="phdr"><a href="profile.php?user=' . $user['id'] . '"><b>' . $lng['profile'] . '</b></a> | ' . $lng_profile['activity'] . '</div>';
$menu = array(
    (!$mod ? '<b>' . $lng['messages'] . '</b>' : '<a href="profile.php?act=activity&amp;user=' . $user['id'] . '">' . $lng['messages'] . '</a>'),
    ($mod == 'topic' ? '<b>' . $lng['themes'] . '</b>' : '<a href="profile.php?act=activity&amp;mod=topic&amp;user=' . $user['id'] . '">' . $lng['themes'] . '</a>'),
    ($mod == 'comments' ? '<b>' . $lng['comments'] . '</b>' : '<a href="profile.php?act=activity&amp;mod=comments&amp;user=' . $user['id'] . '">' . $lng['comments'] . '</a>'),
);
echo '<div class="topmenu">' . functions::display_menu($menu) . '</div>' .
     '<div class="user"><p>' . functions::display_user($user, array('iphide' => 1,)) . '</p></div>';
switch ($mod) {
    case 'comments':
        /*
        -----------------------------------------------------------------
        Список сообщений в Гостевой
        -----------------------------------------------------------------
        */
        $total = $db->query("SELECT COUNT(*) FROM `guest` WHERE `user_id` = '" . $user['id'] . "'" . ($rights >= 1 ? '' : " AND `adm` = '0'"))->fetch_row()[0];
        echo '<div class="phdr"><b>' . $lng['comments'] . '</b></div>';
        if ($total > $kmess) echo '<div class="topmenu">' . functions::display_pagination('profile.php?act=activity&amp;mod=comments&amp;user=' . $user['id'] . '&amp;', $start, $total, $kmess) . '</div>';
        $req = $db->query("SELECT * FROM `guest` WHERE `user_id` = '" . $user['id'] . "'" . ($rights >= 1 ? '' : " AND `adm` = '0'") . " ORDER BY `id` DESC LIMIT $start, $kmess");
        if ($req->num_rows) {
            $i = 0;
            while ($res = $req->fetch_assoc()) {
                echo ($i % 2 ? '<div class="list2">' : '<div class="list1">') . functions::checkout($res['text'], 2, 1) . '<div class="sub">' .
                     '<span class="gray">(' . functions::display_date($res['time']) . ')</span>' .
                     '</div></div>';
                ++$i;
            }
        } else {
            echo '<div class="menu"><p>' . $lng_profile['guest_empty'] . '</p></div>';
        }
        break;

    case 'topic':
        /*
        -----------------------------------------------------------------
        Список тем Форума
        -----------------------------------------------------------------
        */
        $total = $db->query("SELECT COUNT(*) FROM `forum` WHERE `user_id` = '" . $user['id'] . "' AND `type` = 't'" . ($rights >= 7 ? '' : " AND `close`!='1'"))->fetch_row()[0];
        echo '<div class="phdr"><b>' . $lng['forum'] . '</b>: ' . $lng['themes'] . '</div>';
        if ($total > $kmess) echo '<div class="topmenu">' . functions::display_pagination('profile.php?act=activity&amp;mod=topic&amp;user=' . $user['id'] . '&amp;', $start, $total, $kmess) . '</div>';
        $req = $db->query("SELECT * FROM `forum` WHERE `user_id` = '" . $user['id'] . "' AND `type` = 't'" . ($rights >= 7 ? '' : " AND `close`!='1'") . " ORDER BY `id` DESC LIMIT $start, $kmess");
        if ($req->num_rows) {
            $i = 0;
            while ($res = $req->fetch_assoc()) {
                $post = $db->query("SELECT * FROM `forum` WHERE `refid` = '" . $res['id'] . "'" . ($rights >= 7 ? '' : " AND `close`!='1'"->fetch_assoc() . " ORDER BY `id` ASC LIMIT 1"));
                $section = $db->query("SELECT * FROM `forum` WHERE `id` = '" . $res['refid'] . "'")->fetch_assoc();
                $category = $db->query("SELECT * FROM `forum` WHERE `id` = '" . $section['refid'] . "'")->fetch_assoc();
                $text = mb_substr($post['text'], 0, 300);
                $text = functions::checkout($text, 2, 1);
                echo ($i % 2 ? '<div class="list2">' : '<div class="list1">') .
                     '<a href="' . $set['homeurl'] . '/forum/index.php?id=' . $res['id'] . '">' . $res['text'] . '</a>' .
                     '<br />' . $text . '...<a href="' . $set['homeurl'] . '/forum/index.php?id=' . $res['id'] . '"> &gt;&gt;</a>' .
                     '<div class="sub">' .
                     '<a href="' . $set['homeurl'] . '/forum/index.php?id=' . $category['id'] . '">' . $category['text'] . '</a> | ' .
                     '<a href="' . $set['homeurl'] . '/forum/index.php?id=' . $section['id'] . '">' . $section['text'] . '</a>' .
                     '<br /><span class="gray">(' . functions::display_date($res['time']) . ')</span>' .
                     '</div></div>';
                ++$i;
            }
        } else {
            echo '<div class="menu"><p>' . $lng['list_empty'] . '</p></div>';
        }
        break;

    default:
        /*
        -----------------------------------------------------------------
        Список постов Форума
        -----------------------------------------------------------------
        */
        $total = $db->query("SELECT COUNT(*) FROM `forum` WHERE `user_id` = '" . $user['id'] . "' AND `type` = 'm'" . ($rights >= 7 ? '' : " AND `close`!='1'"))->fetch_row()[0];
        echo '<div class="phdr"><b>' . $lng['forum'] . '</b>: ' . $lng['messages'] . '</div>';
        if ($total > $kmess) echo '<div class="topmenu">' . functions::display_pagination('profile.php?act=activity&amp;user=' . $user['id'] . '&amp;', $start, $total, $kmess) . '</div>';
        $req = $db->query("SELECT * FROM `forum` WHERE `user_id` = '" . $user['id'] . "' AND `type` = 'm' " . ($rights >= 7 ? '' : " AND `close`!='1'") . " ORDER BY `id` DESC LIMIT $start, $kmess");
        if ($req->num_rows) {
            $i = 0;
            while ($res = $req->fetch_assoc()) {
                $topic = $db->query("SELECT * FROM `forum` WHERE `id` = '" . $res['refid'] . "'")->fetch_assoc();
                $section = $db->query("SELECT * FROM `forum` WHERE `id` = '" . $topic['refid'] . "'")->fetch_assoc();
                $category = $db->query("SELECT * FROM `forum` WHERE `id` = '" . $section['refid'] . "'")->fetch_assoc();
                $text = mb_substr($res['text'], 0, 300);
                $text = functions::checkout($text, 2, 1);
                $text = preg_replace('#\[c\](.*?)\[/c\]#si', '<div class="quote">\1</div>', $text);
                echo ($i % 2 ? '<div class="list2">' : '<div class="list1">') .
                     '<a href="' . $set['homeurl'] . '/forum/index.php?id=' . $topic['id'] . '">' . $topic['text'] . '</a>' .
                     '<br />' . $text . '...<a href="' . $set['homeurl'] . '/forum/index.php?act=post&amp;id=' . $res['id'] . '"> &gt;&gt;</a>' .
                     '<div class="sub">' .
                     '<a href="' . $set['homeurl'] . '/forum/index.php?id=' . $category['id'] . '">' . $category['text'] . '</a> | ' .
                     '<a href="' . $set['homeurl'] . '/forum/index.php?id=' . $section['id'] . '">' . $section['text'] . '</a>' .
                     '<br /><span class="gray">(' . functions::display_date($res['time']) . ')</span>' .
                     '</div></div>';
                ++$i;
            }
        } else {
            echo '<div class="menu"><p>' . $lng['list_empty'] . '</p></div>';
        }
}
echo '<div class="phdr">' . $lng['total'] . ': ' . $total . '</div>';
if ($total > $kmess) {
    echo '<div class="topmenu">' . functions::display_pagination('profile.php?act=activity' . ($mod ? '&amp;mod=' . $mod : '') . '&amp;user=' . $user['id'] . '&amp;', $start, $total, $kmess) . '</div>' .
         '<p><form action="profile.php?act=activity&amp;user=' . $user['id'] . ($mod ? '&amp;mod=' . $mod : '') . '" method="post">' .
         '<input type="text" name="page" size="2"/>' .
         '<input type="submit" value="' . $lng['to_page'] . ' &gt;&gt;"/>' .
         '</form></p>';
}
?>