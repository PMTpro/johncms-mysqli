<?php
/**
 * @package     JohnCMS
 * @link        http://johncms.com
 * @license     http://johncms.com/license/
 * @author      http://johncms.com/about/
 * @version     VERSION.txt (see attached file)
 * @copyright   Copyright (C) 2008-2011 JohnCMS Community
 */

defined('_IN_JOHNCMS') or die('Error: restricted access');

/**
 * Голосуем за фотографию
 */
if (!$img) {
    echo functions::display_error($lng['error_wrong_data']);
    require('../incfiles/end.php');
    exit;
}

$ref = isset($_SERVER['HTTP_REFERER']) && !empty($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'album.php';

$check = $db->query("SELECT * FROM `cms_album_votes` WHERE `user_id` = '$user_id' AND `file_id` = '$img' LIMIT 1");
if ($check->num_rows) {
    header('Location: ' . $ref);
    exit;
}

$req = $db->query("SELECT * FROM `cms_album_files` WHERE `id` = '$img' AND `user_id` != '$user_id'");
if ($req->num_rows) {
    $res = $req->fetch_assoc();

    switch ($mod) {
        case 'plus':
            /**
             * Отдаем положительный голос
             */
            $db->query("INSERT INTO `cms_album_votes` SET
                `user_id` = '$user_id',
                `file_id` = '$img',
                `vote` = '1'
            ");
            $db->query("UPDATE `cms_album_files` SET `vote_plus` = '" . ($res['vote_plus'] + 1) . "' WHERE `id` = '$img'");
            break;

        case 'minus':
            /**
             * Отдаем отрицательный голос
             */
            $db->query("INSERT INTO `cms_album_votes` SET
                `user_id` = '$user_id',
                `file_id` = '$img',
                `vote` = '-1'
            ");
            $db->query("UPDATE `cms_album_files` SET `vote_minus` = '" . ($res['vote_minus'] + 1) . "' WHERE `id` = '$img'");
            break;
    }

    header('Location: ' . $ref);
} else {
    echo functions::display_error($lng['error_wrong_data']);
}
