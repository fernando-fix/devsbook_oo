<?php
require_once 'models/Post.php';
require_once 'dao/UserRelationDaoMysql.php';
require_once 'dao/UserDaoMysql.php';
require_once 'dao/PostLikeDaoMysql.php';
require_once 'dao/PostCommentDaoMysql.php';

class PostDaoMysql implements PostDao
{
    private $pdo;

    public function __construct(PDO $driver)
    {
        $this->pdo = $driver;
    }

    public function insert(Post $p)
    {
        $sql = $this->pdo->prepare("INSERT INTO posts (
            id_user, type, created_at, body) VALUES (
            :id_user, :type, :created_at, :body)");
        $sql->bindValue(':id_user', $p->id_user);
        $sql->bindValue(':type', $p->type);
        $sql->bindValue(':created_at', $p->created_at);
        $sql->bindValue(':body', $p->body);
        $sql->execute();

        return true;
    }   

    public function delete($id_post, $id_user)
    {
        $postLikeDao = new PostLikeDaoMysql($this->pdo);
        $postCommentDao = new PostCommentDaoMysql($this->pdo);

        //1. Verificar se o post existe
        $sql = $this->pdo->prepare("SELECT * FROM posts WHERE id = :id_post AND id_user = :id_user");
        $sql->bindValue(':id_post', $id_post);
        $sql->bindValue(':id_user', $id_user);
        $sql->execute();

        if ($sql->rowCount() > 0) { //post existe
            $post = $sql->fetch(PDO::FETCH_ASSOC);

            //2. Deletar os likes e comments
            $postLikeDao->deleteFromPost($id_post);
            $postCommentDao->deleteFromPost($id_post);

            //3. Deletar a eventual foto
            if ($post['type'] === 'photo') {

                $img = "media/uploads/".$post['body'];
                if(file_exists($img)) {
                    unlink($img);
                }
            }

            //4. Deletar o post
            $sql = $this->pdo->prepare("DELETE FROM posts WHERE id = :id_post AND id_user = :id_user");
            $sql->bindValue(':id_post', $id_post);
            $sql->bindValue(':id_user', $id_user);
            $sql->execute();
        }

        return true;
    }

    public function getHomeFeed($id_user, $page=1, $logged_user)
    {
        $array = ['feed'=>[]];
        $perPage = 4; //qtdd por página
        $offset = ($page - 1) * $perPage; //a partir de qual post vai mostrar

        // 1.pegar a lista de usuarios que eu sigo
        $urDao = new UserRelationDaoMysql($this->pdo);
        $userList = $urDao->getFollowing($id_user);
        $userList[] = $id_user;

        // 2.pegar os posts desses usuários ordenado pela data
        $sql = $this->pdo->query("SELECT * FROM posts WHERE id_user IN (" . implode(',', $userList) . ") ORDER BY created_at DESC, id DESC LIMIT $offset,$perPage");

        if ($sql->rowCount() > 0) {
            $data = $sql->fetchAll(PDO::FETCH_ASSOC);

            // 3.transformar o resultado em objetos e exibir
            $array['feed'] = $this->_postListToObject($data, $id_user, $logged_user);
        }

        //Pegar o total de posts
        $sql = $this->pdo->prepare("SELECT COUNT(*) as c FROM posts WHERE id_user IN (" . implode(',', $userList) . ")");
        $sql->execute();
        $totalData = $sql->fetch();
        $total = $totalData['c'];

        $array['pages'] = ceil($total / $perPage);
        $array['currentPage'] = $page;

        return $array;
    }

    public function getUserFeed($id_user, $page=1, $logged_user)
    {
        $array = ['feed'=>[]];
        $perPage = 4; //qtdd por página
        $offset = ($page - 1) * $perPage; //a partir de qual post vai mostrar

        $sql = $this->pdo->prepare("SELECT * FROM posts WHERE id_user = :id_user ORDER BY created_at DESC LIMIT $offset,$perPage");
        $sql->bindValue(':id_user', $id_user);
        $sql->execute();
        if ($sql->rowCount() > 0) {
            $data = $sql->fetchAll(PDO::FETCH_ASSOC);
            $array['feed'] = $this->_postListToObject($data, $id_user, $logged_user);
        }

        //Pegar o total de posts
        $sql = $this->pdo->prepare("SELECT COUNT(*) as c FROM posts WHERE id_user = :id_user");
        $sql->bindValue(':id_user', $id_user);
        $sql->execute();
        $totalData = $sql->fetch();
        $total = $totalData['c'];

        $array['pages'] = ceil($total / $perPage);
        $array['currentPage'] = $page;

        return $array;
    }


    public function getPhotosFrom($id_user)
    {
        $array = [];

        $sql = $this->pdo->prepare("SELECT * FROM posts WHERE id_user = :id_user AND type = 'photo' ORDER BY created_at DESC");
        $sql->bindValue(':id_user', $id_user);
        $sql->execute();

        if ($sql->rowCount() > 0) {
            $data = $sql->fetchAll(PDO::FETCH_ASSOC);
            $array = $this->_postListToObject($data, $id_user);
        }
        return $array;
    }


    private function _postListToObject($post_list, $id_user, $logged_user=0)
    {
        //3.1 retornar array com objetos
        $posts = [];
        $userDao = new UserDaoMysql($this->pdo);
        $postLikeDao = new PostLikeDaoMysql($this->pdo);
        $postCommentDao = new PostCommentDaoMysql($this->pdo);

        foreach ($post_list as $post_item) {
            $newPost = new Post();
            $newPost->id = $post_item['id'];
            $newPost->type = $post_item['type'];
            $newPost->created_at = $post_item['created_at'];
            $newPost->body = $post_item['body'];
            $newPost->mine = false;

            if ($post_item['id_user'] == $logged_user) {
                $newPost->mine = true;
            }

            // Pegar informações do usuário que fez o post
            $newPost->user = $userDao->findById($post_item['id_user']);

            // Informações sobre likes
            $newPost->likeCount = $postLikeDao->getLikeCount($newPost->id);
            $newPost->liked = $postLikeDao->isLiked($newPost->id, $logged_user);

            // Informações sobre COMMENTS
            $newPost->comments = $postCommentDao->getComments($newPost->id);

            $posts[] = $newPost;
        }

        return $posts;
    }
}
