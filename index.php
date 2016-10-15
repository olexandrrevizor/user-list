<?php require_once "parts/header.php";
    $users = getUsers();
?>
    <section class="main-col">
        <div class="container">
            <div class="col-md-12">
                <?php if (isset($users)) : ?>
                    <?php foreach($users as $user) :?>
                        <div class="col-md-6">
                            <div class="media">
                                <?php if (isset($user['user_avatar_url']) && !empty($user['user_avatar_url'])) :?>
                                    <div class="col-md-4">
                                        <img class="media-object" src="<?= $user['user_avatar_url'] ?>" alt="Avatar url">
                                    </div>
                                <?php endif ?>
                                <div class="col-md-8">
                                    <?php if (isset($user['user_name']) && !empty($user['user_name'])) :?>
                                        <a href="<?= 'user.php?id=' . $user['id'] ?>">
                                            <h4 class="media-heading"><?= $user['user_name'] ?></h4>
                                        </a>
                                    <?php endif ?>

                                    <?php if (isset($user['user_phone']) && !empty($user['user_phone'])) :?>
                                        <h4 class="media-heading"><?= $user['user_phone'] ?></h4>
                                    <?php endif ?>
                                    <?php if (isset($user['user_info']) && !empty($user['user_info'])) :?>
                                        <p><?= $user['user_info'] ?></p>
                                    <?php endif ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach?>
                <?php else: ?>
                    <h2>User not found!</h2>
                <?php endif?>
            </div>
        </div>
    </section>
<?php require_once "parts/footer.php"; ?>
