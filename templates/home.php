<?php
?>

<h1>Home</h1>

<p><?=$msg?></p>
<p>Hello, <?=$nickname?>!</p>

<p><a href='<?=URL_BASE?>/diary'>Diary</a></p>
<p><a href='<?=URL_BASE?>/settings'>Settings</a></p>

<p>Session data:</p>
<pre>
    <?php
    echo var_export($_SESSION, false);
    ?>

</pre>

<p><a href='<?=URL_BASE?>/logout'>Logout</a></p>

<?php
?>
