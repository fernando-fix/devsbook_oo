<?php

require_once 'config.php';
require_once 'models/Auth.php';
require_once 'dao/UserDaoMysql.php';

$auth = new Auth($pdo, $base);
$userInfo = $auth->checkToken();
$activeMenu = 'config';

$postDao = new UserDaoMysql($pdo);

require 'partials/header.php';
require 'partials/menu.php';
?>
<section class="feed mt-10">

    <h2>Configurações</h2>

    <?php if (!empty($_SESSION['flash'])) : ?>
        <?= $_SESSION['flash']; ?>
        <?php $_SESSION['flash'] = ''; ?>
    <?php endif; ?>

    <form action="configuracoes_action.php" enctype="multipart/form-data" method="post" class="config-form">

        <label for="avatar">Novo avatar:</label>
        <input type="file" name="avatar" id="avatar">
        <img class="mini" src="<?= $base ?>/media/avatars/<?= $userInfo->avatar ?>">

        <label for="cover">Nova capa:</label>
        <input type="file" name="cover" id="cover">
        <img class="mini" src="<?= $base ?>/media/covers/<?= $userInfo->cover ?>">

        <hr>

        <label for="name">Nome completo:</label>
        <input type="text" name="name" id="name" autocomplete="off" value="<?= $userInfo->name ?>">

        <label for="email">Email:</label>
        <input type="email" name="email" id="email" autocomplete="off" value="<?= $userInfo->email ?>">

        <label for="birthdate">Data de nascimento:</label>
        <input type="text" name="birthdate" id="birthdate" autocomplete="off" value="<?= date('d/m/Y', strtotime($userInfo->birthdate)) ?>">

        <label for="city">Cidade:</label>
        <input type="text" name="city" id="city" autocomplete="off" value="<?= $userInfo->city ?>">

        <label for="work">Trabalho:</label>
        <input type="text" name="work" id="work" autocomplete="off" value="<?= $userInfo->work ?>">

        <hr>

        <label for="work">Nova senha:</label>
        <input type="password" name="password" id="password" autocomplete="off">

        <label for="password_confirmation">Confirme a senha:</label>
        <input type="password" name="password_confirmation" id="password_confirmation" autocomplete="off">

        <br>

        <button class="button">Salvar</button>

    </form>

</section>

<script src="https://unpkg.com/imask"></script>
<script>
    IMask(
        document.getElementById("birthdate"), {
            mask: '00/00/0000'
        }
    );
</script>

<?php require 'partials/footer.php'; ?>