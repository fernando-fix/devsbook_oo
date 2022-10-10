<?php
class Post {
    public $id;
    public $idUser;
    public $type; //text or photo
    public $created_at;
    public $body;
}

interface PostDao {
    public function insert(Post $p);
    public function delete($id_post, $id_user);
    public function getHomeFeed($id_user, $page=1, $logged_user);
    public function getUserFeed($user_id, $page=1, $logge_ser);
    public function getPhotosFrom($user_id);
}