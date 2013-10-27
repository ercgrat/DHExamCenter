function administrate_init()
{
    document.getElementById("instructor_button").onclick = add_instructor;
    document.getElementById("institution_button").onclick = add_institution;
}

function add_instructor ()
{
    var institution_input = document.getElementById("institution_selector");
    var instructor_input = document.getElementById("instructor_username");
    if(instructor_input.value == "")
    {
        instructor_input.nextElementSibling.innerHTML = "The username must be between 1 and 32 characters in length.";
        return;
    }
    
    var req = new XMLHttpRequest();
    var getString = "administrate-add_instructor.php?username=" + instructor_input.value + "&instid=" + institution_input.value;
    req.onreadystatechange = function() {
        if (req.readyState==4 && req.status==200)
        {
            var response = req.responseText.split("|", 2);
            if(response[0] == "0")
            {
                instructor_input.nextElementSibling.innerHTML = response[1];
                document.getElementById("instructor_button").nextElementSibling.innerHTML = "";
            }
            else
            {
                instructor_input.nextElementSibling.innerHTML = "";
                document.getElementById("instructor_button").nextElementSibling.innerHTML = response[1];
                reload_pending_users();
                reload_current_faculty();
            }
        }
    }
    req.open("GET", getString, false);
    req.send();
}

function add_institution ()
{
    var input = document.getElementById("institution_name");
    if(input.value == "")
    {
        input.nextElementSibling.innerHTML = "Enter a name between 1 and 128 characters.";
        return;
    }
    
    var req = new XMLHttpRequest();
    var getString = "administrate-add_institution.php?inst_name=" + input.value;
    req.onreadystatechange = function() {
        if (req.readyState==4 && req.status==200)
        {
            var response = req.responseText.split("|", 2);
            if(response[0] == "0")
            {
                input.nextElementSibling.innerHTML = response[1];
                document.getElementById("institution_button").nextElementSibling.innerHTML = "";
            }
            else
            {
                input.nextElementSibling.innerHTML = "";
                document.getElementById("institution_button").nextElementSibling.innerHTML = response[1];
                reload_pending_users();
                reload_institutions();
            }
        }
    }
    req.open("GET", getString, false);
    req.send();
}

function reload_pending_users ()
{
    var req = new XMLHttpRequest();
    var getString = "administrate-fragment-pending_users_table.php";
    req.onreadystatechange = function () {
        if (req.readyState==4 && req.status==200)
        {
            document.getElementById("pending_users_table").innerHTML = req.responseText;
        }
    }
    req.open("GET", getString, false);
    req.send();
}

function reload_current_faculty ()
{
    var req = new XMLHttpRequest();
    var getString = "administrate-fragment-current_faculty_table.php";
    req.onreadystatechange = function () {
        if (req.readyState==4 && req.status==200)
        {
            document.getElementById("current_faculty_table").innerHTML = req.responseText;
        }
    }
    req.open("GET", getString, false);
    req.send();
}

function reload_institutions ()
{
    var req = new XMLHttpRequest();
    var getString = "administrate-fragment-institutions_selector.php";
    req.onreadystatechange = function () {
        if (req.readyState==4 && req.status==200)
        {
            document.getElementById("institutions_selector").innerHTML = req.responseText;
        }
    }
    req.open("GET", getString, false);
    req.send();
}

addLoadEvent(administrate_init);