<?php //user_create.php
require_once "authenticate.php";
require_once "login.php";
require_once "layout.php";

function output_creation_form($user_len_invalid, $fullname_len_invalid, $pass_len_invalid, $acc_len_invalid, $username_invalid, $account_used, $account_exists, $username_exists, $passwords_match)
{   
    global $db_handle;
    
    echo '<div id="login">';
    echo '<h2>Create Account</h2>';
    echo '<form method="post" action="user_create.php">';
    
    $inst_query = $db_handle->prepare("SELECT nstidiyay, amenay FROM nstiyay");
    $inst_query->execute();
    $inst_query->store_result();
    $inst_query->bind_result($instid, $instname);
    
    echo "<label>Institution: <select name='inst'>";
    while($inst_query->fetch())
    {
        echo "<option value='$instid'>$instname</option>'";
    }
    echo "</select></label>";
    
    echo '<label>School username: <span class="warning">*</span> <br/><input type="text" name="account" size="35" maxlength="32" value="'.sanitizeString($_POST['account']).'"/> <span class="note">Must be registered by your instructor.</span></label>';
    if($acc_len_invalid) { echo '<p class="warning">Use your unique school username. Your instructor must add your username to their course before you can register.</p>'; }
    if($account_used) { echo '<p class="warning">An account under your institution has already been created using this username. If you have already made an account and forget your password, please contact your instructor.</p>'; }
    if(!$account_exists) { echo '<p class="warning">School username has not been registered by an instructor. Contact your instructor to help set up your account.</p>'; }
    echo '<br/><label>Username (e.g., jsmith452): <span class="warning">*</span> <br/><input type="text" name="username" size="35" maxlength="30" value="'.sanitizeString($_POST['username']).'"/> <span class="note"> 4-30 characters (a-z, A-Z, 0-9, -, _). Used to login to eXam Center.</span></label>';
    if($user_len_invalid) { echo '<p class="warning">Username should be between 4 and 30 characters.</p>'; }
    if($username_invalid) { echo '<p class="warning">Username can only contain a combination of letters, numbers, hyphens, and underscores.</p>'; }
    if($username_exists) { echo '<p class="warning">Username already taken.</p>'; }
    echo '<label>Full Name (e.g., John Smith): <br/><input type="text" name="fullname" size="35" maxlength="128" value="'.sanitizeString($_POST['fullname']).'"/></label>';
    if($fullname_len_invalid) { echo '<p class="warning">Full name should be less than 128 characters.</p>'; }
    echo '<br/><label>Password: <span class="warning">*</span> <br/><input type="password" name="password" size="35" maxlength="35"/> <span class="note"> 8-30 characters</span></label>';
    echo '<label>Confirm Password: <span class="warning">*</span> <br/><input type="password" name="password2" size="35" maxlength="35"/> <span class="note">Passwords must match.</span></label>';
    if($pass_len_invalid) { echo '<p class="warning">Password should be between 8 and 30 characters of any type.</p>'; }
    if(!$passwords_match) { echo '<p class="warning">Passwords must match.</p>'; }
    
    echo <<<_FORMEND
            <br/>
            <label><span class="warning">* = required field</span></label>
            <br/>
            <input type="submit" value="Create User"/>
        </form>
    </div>
_FORMEND;

}

session_start();
session_regenerate_id();

$input_valid = TRUE;
$account_exists = TRUE;
$passwords_match = TRUE;

if(isset($_SESSION['user']) && $_SESSION['user']->logged_in)
{
    if(isset($_SESSION['redirect'])) { headers("Location: https://". $_SERVER["HTTP_HOST"] . $_SESSION['redirect']); }
    else { header("Location: https://". $_SERVER["HTTP_HOST"] . "/index.php"); }
    exit();
}
else if(isset($_POST['account'], $_POST['username'], $_POST['password']))
{   
    if(strlen($_POST['username']) < 4 || strlen($_POST['username']) > 30) { $user_len_invalid = TRUE; $input_valid = FALSE; }
    if(isset($_POST['fullname'])) { if(strlen($_POST['fullname']) > 128) { $fullname_len_invalid = TRUE; $input_valid = FALSE; } }
    if(strlen($_POST['password']) < 8 || strlen($_POST['password']) > 35) { $pass_len_invalid = TRUE; $input_valid = FALSE; }
    if(strlen($_POST['account']) < 4 || strlen($_POST['account']) > 32) { $acc_len_invalid = TRUE; $input_valid = FALSE; }
    if($_POST['password'] != $_POST['password2']) { $passwords_match = FALSE; $input_valid = FALSE; }
    $characters = array('a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z', 'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', '-', '_', '0', '9', '8', '7', '6', '5', '4', '3', '2', '1');
    for($i = 0; $i < strlen($username); $i++)
    {
        if(!in_array(substr($username,$i,1), $characters))
        {
            $username_invalid = TRUE;
            $input_valid = FALSE;
        }
    }
}
else
{
    $input_valid = FALSE;
}

if($input_valid)
{
    $account = $db_handle->real_escape_string($_POST['account']);
    $username = $db_handle->real_escape_string($_POST['username']);
    $fullname = $db_handle->real_escape_string($_POST['fullname']);
    $password = $db_handle->real_escape_string($_POST['password']);
    $inst = $db_handle->real_escape_string($_POST['inst']);
    $user_token = hash("sha256", $username);
    
    $username_query = $db_handle->prepare("SELECT * FROM sersuyay WHERE sernameuyay = ?");
    $username_query->bind_param("s", $user_token);
    $username_query->execute();
    $username_query->store_result();
    if($username_query->num_rows() == 1) { $username_exists = TRUE; }
    
    $account_query = $db_handle->prepare("SELECT nstidiyay FROM ittspay WHERE ccountayay = ?");
    $account_query->bind_param("s", $account);
    $account_query->execute();
    $account_query->store_result();
    if($account_query->num_rows() == 0)
    {
        $user_query = $db_handle->prepare("SELECT * FROM sersuyay WHERE ccountayay = ? AND nstidiyay = ?");
        $user_query->bind_param("si", $account, $inst);
        $user_query->execute();
        $user_query->store_result();
        if($user_query->num_rows() != 0)
        {
            $account_exists = TRUE;
            $account_used = TRUE;
        }
        else
        {
            $account_exists = FALSE;
        }
    }
    else
    {
        $account_query->bind_result($instid);
        $account_query->fetch();
        if($instid != $inst)
        {
            $account_exists = FALSE;
            $input_valid = FALSE;
        }
    }
}

if(!$input_valid || $username_exists || $account_used || !$account_exists)
{
    output_start("", $_SESSION['user']);
    output_creation_form($user_len_invalid, $fullname_len_invalid, $pass_len_invalid, $acc_len_invalid, $username_invalid, $account_used, $account_exists, $username_exists, $passwords_match);
    output_end();
}
else
{
    $created = User::create_user($account, $username, $fullname, $password, $inst);
    if($created) { $_SESSION['user_created'] = 1; }
    else { $_SESSION['user_created'] = 0; }
    header("Location: https://".  $_SERVER["HTTP_HOST"] . "/user_created.php");
}

?>