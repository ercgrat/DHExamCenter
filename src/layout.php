<?php //layout.php
require_once "login.php";
require_once "authenticate.php";

function output_start($header, $user)
{
    echo <<<_PART1
    <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
    <html xmlns="http://www.w3.org/1999/xhtml">
        <head>
            <meta http-equiv="Content-Type" content="text/html;charset=utf-16"/>
            <title>DH eXam Center</title>
            <link rel="stylesheet" type="text/css" href="index.css"/>
            <script type="text/javascript" src="layout.js"></script>
_PART1;

    echo $header;
    
    echo <<<_PART2
    </head>
    <body>
        <div id="body">
        <div id="shadow_box">
            <div id="banner">
                <a class="title" href="index.php">DH eXam Center</a>
                <div id="subscript">
_PART2;

    if($user->logged_in == 1)
    {
        if(isset($user->fullname)) { echo "Hi, ".$user->fullname.". "; }
        else { echo "Hi, ".$user->username.". "; }
        echo "<a href='user_logout.php'>Logout</a>";
    }
    else
    {
        echo "You are not logged in. ";
        echo "<a href='user_login.php'>Login</a>";
    }
    
    echo <<<_PART3
    </div>
            </div>
            <div id="content-wrapper">
                <div id="menu">
                    <p>NAVIGATION</p>
                    <ul>
                    <li onclick="window.location='about.php'"><a href="about.php">About</a></li>
_PART3;

    if($user->logged_in && $user->role >= 2) { echo "<li onclick='window.location=\"administrate.php\"'><a href='administrate.php'>Admin Dashboard</a></li>"; }
    if($user->logged_in && $user->role >= 1) { echo "<li onclick='window.location=\"instructor.php\"'><a href='instructor.php'>Instructor Dashboard</a></li>"; }
    if($user->logged_in && $user->role == 0) { echo "<li onclick='window.location=\"student.php\"'><a href='student.php'>Student Dashboard</a></li>"; }

    echo <<<_PART4
                        
                        <li onclick="window.location='progress.php'"><a href="progress.php">View progress</a></li>
                    </ul>
                </div>
                <div id="content">
_PART4;
}

function output_end()
{
    echo <<<_END
                    </div>
                </div>
            </div>
            </div>
            <div id="layout_bottom_buffer" style="height:100px"/>
        </body>
    </html>
_END;
}

function output_error($header, $user, $error_msg) // Error display for when no content has been displayed on the page.
{
    output_start($header, $user);
    echo "<p>$error_msg</p>";
    output_end();
    die();
}

function output_runtime_error($error_msg) // Error display for when the beginning of the page has already been displayed.
{
    echo "<p>$error_msg</p>";
    output_end();
    die();
}

function save_log($log_string)
{
    $log_query = $db_handle->prepare("INSERT INTO error_logs(log) VALUES(?)");
    $log_query->bind_param("s", $log_string);
    $log_query->execute();
}

function string_with_space_preserved($str)
{
    $str = htmlentities($str);
    $output = "";
    for($i = 0; $i < strlen($str); $i++)
    {
        if($str{$i} == " ")
        {
            $count = 1;
            for($j = $i + 1; $j < strlen($str); $j++)
            {
                if($str{$j} == " ") { $count++; }
                else { break; }
            }
            if($count > 1)
            {
                for($s = 0; $s < $count; $s++)
                {
                    $output .= "&nbsp";
                }
                $i = $j-1;
            }
            else
            {
                $output .= " ";
            }
        }
        else
        {
            $output .= $str{$i};
        }
    }
    return nl2br($output);
}
?>