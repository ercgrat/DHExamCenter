<?php //sanitize.php
require_once "login.php";

function sanitizeString($var)
{
    if(get_magic_quotes_gpc()) $var = stripslashes($var);
    $var = htmlentities($var);
    $var = strip_tags($var);
    return $var;
}
function sanitizeMySQL($var)
{
    global $db_handle;
    $var = mysqli_real_escape_string($db_handle, $var);
    $var = sanitizeString($var);
    return $var;
}

?>