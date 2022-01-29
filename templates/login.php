<?php
?>

<h1>Login</h1>

<p><?=$msg?></p>

<form id="login_form" action='<?php echo URL_BASE; ?>/login' method='POST'>
    Email: <input type="text" name="un"><br />
    Password: <input type="password" name="pw"><br />
    <input type="submit" name="btn_submit" value=" Submit ">
</form>

<p>No account? <a href='<?php echo URL_BASE; ?>/signup'>Sign up</a></p>

<?php
?>
