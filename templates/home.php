<?php
?>

<h1>Home</h1>

<p><?=$msg?></p>
<p>Hello, <?=$nickname?>!</p>

<p>Session data:</p>
<pre>
    <?php
    echo var_export($_SESSION, false);
    ?>

</pre>

<p><a href='<?php echo URL_BASE; ?>/logout'>Logout</a></p>

<?php
?>
