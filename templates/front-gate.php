<?php
?>

<p>Are you of legal age to view cannabis related content where you are?</p>

<form id="front_gate_form_yes" action='<?php echo URL_BASE; ?>/front-gate' method='POST'>
<input type="submit" name="front_gate_answer" value="Yes" />
</form>

<br />

<form id="front_gate_form_no" action='<?php echo URL_BASE; ?>/front-gate' method='POST'>
<input type="submit" name="front_gate_answer" value="No" />
</form>

<?php
?>
