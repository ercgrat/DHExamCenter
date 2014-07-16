<?php //layout.php
require_once $_SERVER["DOCUMENT_ROOT"]."/login.php";
require_once $_SERVER["DOCUMENT_ROOT"]."/authenticate.php";
$root = $_SERVER["HTTP_HOST"];

function output_start($header, $user)
{
    global $root;
    
    echo <<<_PART1
    <!DOCTYPE html>
    <html>
        <head>
            <meta http-equiv="Content-Type" content="text/html;charset=utf-8"/>
            <title>DH eXam Center</title>
            <link rel="stylesheet" type="text/css" href="https://$root/index.css"/>
            <script type="text/javascript" src="https://$root/layout.js"></script>
_PART1;

    echo $header;
    
    echo <<<_PART2
    </head>
    <body>
        <div id="body">
        <div id="shadow_box">
            <div id="banner">
                <a class="title" href="https://$root">DH eXam Center</a>
                <div id="subscript">
_PART2;

    if($user->logged_in == 1)
    {
        if(isset($user->fullname)) { echo "Hi, <a href='https://$root/user.php'>".$user->fullname."</a>. "; }
        else { echo "Hi, <a href='https://$root/user.php'>".$user->username."</a>. "; }
        echo "<a href='https://$root/user_logout.php'>Logout</a>";
    }
    else
    {
        echo "You are not logged in. ";
        echo "<a href='https://$root/user_login.php'>Login</a>";
    }
    
    echo <<<_PART3
    </div>
            </div>
            <div id="content-wrapper">
                <div id="menu">
                    <p>NAVIGATION</p>
                    <ul>
                    <li onclick="window.location.assign('https://$root/about.php')"><img src='https://$root/nav_about.png' alt='[ ]'/><a href="https://$root/about.php">About</a></li>
_PART3;

    if($user->logged_in && $user->role >= 2) { echo "<li onclick='window.location.assign(\"https://$root/administrate\")'><img src='https://$root/nav_admin.png' alt='[ ]'/><a href='https://$root/administrate'>Administrator</a></li>"; }
    if($user->logged_in && $user->role >= 1) { echo "<li onclick='window.location.assign(\"https://$root/instructor.php\")'><img src='https://$root/nav_dashboard.png' alt='[ ]'/><a href='https://$root/instructor.php'>Instructor</a></li>"; }
    if($user->logged_in && $user->role == 0) { echo "<li onclick='window.location.assign(\"https://$root/student.php\")'><img src='https://$root/nav_dashboard.png' alt='[ ]'/><a href='https://$root/student.php'>Student</a></li>"; }

    echo <<<_PART4
                        
                        <li onclick="window.location.assign('https://$root/progress')"><img src='https://$root/nav_progress.png' alt='[ ]'/><a href="https://$root/progress">Progress</a></li>
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