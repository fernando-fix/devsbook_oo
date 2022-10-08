<?php
require_once 'config.php';
require_once 'models/Auth.php';
require_once 'dao/UserRelationDaoMysql.php';
require_once 'dao/UserDaoMysql.php';

$auth = new Auth($pdo, $base);
$userInfo = $auth->checkToken();

$id = filter_input(INPUT_GET, 'id');

if($id) {
    $newUserRelationDao = new UserRelationDaoMysql($pdo);
    $newUserRelation = new UserRelation();

    $newUserRelation->user_from = $userInfo->id;
    $newUserRelation->user_to = $id;

    if($newUserRelationDao->isFollowing($userInfo->id, $id)) {
        //deletar
        $newUserRelationDao->delete($newUserRelation);
    } else {
        //inserir
        $newUserRelationDao->insert($newUserRelation);
    }
    header("Location: ".$base."/perfil.php?id=".$id);
    exit;
}

header("Location: ".$base);
exit;