<?php

include(ROOT_PATH . "/app/database/db.php");
include(ROOT_PATH . "/app/helpers/middleware.php");


$table = 'comments';

$errors = array();
$id = '';
$comment = '';


$comments = selectAll($table);

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $comments = selectOne($table, ['id' => $id]);
    $id = $comments['id'];
    $comment = $comments['comment'];

}

if (isset($_GET['del_id'])) {
    adminOnly();
    $id = $_GET['del_id'];
    $count = delete($table, $id);
    $_SESSION['message'] = 'Comment deleted successfully';
    $_SESSION['type'] = 'success';
    header('location: ' . BASE_URL . '/admin/Comments/index.php');
    exit();
}

