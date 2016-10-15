<?php require_once "parts/header.php"; ?>
<section class="main-col">
    <div class="container">
        <div class="col-md-8 col-md-offset-2">
            <form onsubmit="return NS.ajax.sendAjax(this)">
                <div class="form-group">
                    <label for="user_name">Contact Name</label>
                    <input type="text" class="form-control" id="user_name" placeholder="Name">
                </div>
                <div class="form-group">
                    <label for="user_email">Email Address</label>
                    <input type="email" class="form-control" id="user_email" placeholder="Email">
                </div>
                <div class="form-group">
                    <label for="user_phone">Phone</label>
                    <input type="text" class="form-control" id="user_phone" placeholder="Phone">
                </div>
                <div class="form-group">
                    <label for="user_about">About Info</label>
                    <textarea id="user_about"  class="form-control" placeholder="About"></textarea>
                </div>
                <div class="form-group avatar-button">
                    <label for="user_avatar">Avatar</label>
                    <input type="hidden" id="user_avatar_url">
                    <input type="file" id="user_avatar" accept="image/jpeg, image/png, image/jpg" disabled>
                </div>
                <button type="submit" class="btn btn-success">Submit</button>
            </form>
        </div>
    </div>
</section>

<?php require_once "parts/footer.php"; ?>
