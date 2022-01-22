<?php
?>

<h1>Create Account</h1>

<p><?=$msg?></p>

<form id="signup_form" action='<?php echo URL_BASE; ?>/signup' method='POST'>
    Email: <input type="text" name="un"><br />
    Password: <input type="password" name="pw"><br />
    Nickname: <input type="text" name="nickname"><br />
    <input type="submit" name="btn_submit" value=" Submit ">
</form>

<?php
?>
