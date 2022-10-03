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
    public function getHomeFeed($id_user);
    public function getUserFeed($user_id);
    public function getPhotosFrom($user_id);
}