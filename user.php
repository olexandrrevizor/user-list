<?php require_once "parts/header.php";
$current_user = getCurrentUser();
?>
<section class="main-col">
    <div class="container">
        <div class="col-md-8 col-md-offset-2">
            <?php if ($current_user !== NULL) : ?>
                    <?php if (isset($current_user['user_avatar_url']) && !empty($current_user['user_avatar_url'])) :?>
                        <div class="col-md-4">
                            <img class="media-object" src="<?= $current_user['user_avatar_url'] ?>" alt="...">
                        </div>
                    <?php endif ?>
                    <div class="col-md-8">
                        <?php if (isset($current_user['user_name']) && !empty($current_user['user_name'])) :?>
                        <h4 class="media-heading"><?= $current_user['user_name'] ?></h4>
                        <?php endif ?>

                        <?php if (isset($current_user['user_phone']) && !empty($current_user['user_phone'])) :?>
                        <h4 class="media-heading"><?= $current_user['user_phone'] ?></h4>
                        <?php endif ?>

                        <?php if (isset($current_user['user_info']) && !empty($current_user['user_info'])) :?>
                             <p><?= $current_user['user_info'] ?></p>
                        <?php endif ?>
                    </div>
            <?php else : ?>
                <h2>User not found!</h2>
            <?php endif ?>
        </div>
    </div>
</section>

<?php require_once "parts/footer.php"; ?>
