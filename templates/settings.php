<?php
// Template has access to:
// $user_id, $user_email, $nickname, $account_create_date, $msg
?>

<h1>Settings</h1>

<p><?=$msg?></p>
<p>Hello, <?=$nickname?>!</p>

<p><a href='<?=URL_BASE?>/home'>Home</a></p>
<p><a href='<?=URL_BASE?>/diary'>Diary</a></p>

<div id="user_config">
<table>
    <tr>
        <td>
            Email: 
        </td>
        <td>
            <span id="user_email_wrapper"><?=$user_email?>
                &nbsp;
                <input type="button" id="user_email_update_button" 
                    name="user_email_update_button" value="Update" 
                    onclick="javascript:update_user_email('<?=$user_email?>');">
            </span>
        </td>
    </tr>
    <tr>
        <td>
            Nickname: 
        </td>
        <td>
            <span id="nickname_wrapper"><?=$nickname?>
                &nbsp;
                <input type="button" id="nickname_update_button" 
                    name="nickname_update_button" value="Update" 
                    onclick="javascript:update_nickname('<?=$nickname?>');">
            </span>
        </td>
    </tr>
</table>
</div> <!-- /user_config -->

<div class="break" style="height: 20px;"></div>

<div id="change_password_link" style="display: inline;">
    <a href="javascript:void(0);" onclick="javascript:change_password();">Change password?</a>
</div>

<div id="password_change" style="display: none;">
<table>
    <tr>
        <td>
            Password: 
        </td>
        <td>
            <input type="password" id="pwd_1" name="pwd_1" 
                value="" />
        </td>
    </tr>
    <tr>
        <td>
            Verify Password: 
        </td>
        <td>
            <input type="password" id="pwd_2" name="pwd_2" 
                value="" />
        </td>
    </tr>
    <tr>
        <td colspan="2">
            <input type="button" name="password_update" value="Submit">
        </td>
        <td colspan="2">
            <input type="button" name="password_cancel" value="Cancel" 
                onclick="javascript:cancel_password();">
        </td>
</table>
</div> <!-- /password_change -->

<form id="change_form" name="change_form" action="<?=URL_BASE?>/settings"
    method="post">

<input type="hidden" id="field_being_edited" name="field_being_edited" value="">
<input type="hidden" id="new_value" name="new_value" value="">

</form>

<!--
<p>Session data:</p>
<pre>
    <?php
    echo var_export($_SESSION, false);
    ?>
</pre>

<p>Request data:</p>
<pre>
    <?php
    echo var_export($_REQUEST, false);
    ?>
</pre>
-->

<p><a href='<?=URL_BASE?>/logout'>Logout</a></p>

<script type="text/javascript">

function submit_change_form(field_being_edited = '') {
    var new_value = document.getElementById(field_being_edited).value;
    document.getElementById('field_being_edited').value = field_being_edited;
    document.getElementById('new_value').value = new_value;
    document.getElementById('change_form').submit();
    // console.log("POSTing: " + field_being_edited + " - " + new_value);
}

function update_user_email(current_value) {
    document.getElementById("user_email_wrapper").innerHTML = '' +
        '<input type="text" id="user_email" name="user_email" value="' + current_value + '" />' +
        '&nbsp;' +
        '<input type="button" id="user_email_submit_button" ' +
        '   name="user_email_submit_button" value="Submit change" ' +
        "   onclick='javascript:submit_change_form(\"user_email\");''>" +
        '&nbsp;or&nbsp;' +
        '<a href="javascript:void(0);" ' +
        "   onclick=\"javascript:cancel_user_email('" + current_value + "');\">Cancel</a>";

    document.getElementById("nickname_update_button").style.display = "none";
    // document.getElementById("sort_icon").style.display = "inline";
}

function cancel_user_email(current_value = '') {
    document.getElementById("user_email_wrapper").innerHTML = '' +
        current_value +
        '&nbsp;' +
        '<input type="button" id="user_email_update_button" ' +
        '   name="user_email_update_button" value="Update" ' +
        "   onclick=\"javascript:update_user_email('" + current_value + "');\">";

    document.getElementById("nickname_update_button").style.display = "inline";
}

function update_nickname(current_value = '') {
    document.getElementById("nickname_wrapper").innerHTML = '' +
        '<input type="text" id="nickname" name="nickname" value="' + current_value + '" />' +
        '&nbsp;' +
        '<input type="button" id="nickname_submit_button" ' +
        '   name="nickname_submit_button" value="Submit change" ' +
        "   onclick='javascript:submit_change_form(\"nickname\");''>" +
        '&nbsp;or&nbsp;' +
        '<a href="javascript:void(0);" ' +
        "   onclick=\"javascript:cancel_nickname('" + current_value + "');\">Cancel</a>";

    document.getElementById("user_email_update_button").style.display = "none";
}

function cancel_nickname(current_value = '') {
    document.getElementById("nickname_wrapper").innerHTML = '' +
        current_value +
        '&nbsp;' +
        '<input type="button" id="nickname_update_button" ' +
        '   name="nickname_update_button" value="Update" ' +
        "   onclick=\"javascript:update_nickname('" + current_value + "');\">";

    document.getElementById("user_email_update_button").style.display = "inline";
}

function change_password() {
    document.getElementById("password_change").style.display = "inline";
    document.getElementById("change_password_link").style.display = "none";

    document.getElementById("user_email_update_button").style.display = "none";
    document.getElementById("nickname_update_button").style.display = "none";
}

function cancel_password() {
    document.getElementById("password_change").style.display = "none";
    document.getElementById("change_password_link").style.display = "inline";

    document.getElementById("user_email_update_button").style.display = "inline";
    document.getElementById("nickname_update_button").style.display = "inline";
}

</script>

<?php
?>
