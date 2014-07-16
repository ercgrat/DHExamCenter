function administrate_init()
{
    document.getElementById("instructor_button").onclick = add_instructor;
    document.getElementById("institution_button").onclick = add_institution;
	document.getElementById("passwordchange_button").onclick = change_password;
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
    var getString = "https://" + _administrateRoot + "/add_instructor.php?username=" + instructor_input.value + "&instid=" + institution_input.value;
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
    var getString = "https://" + _administrateRoot + "add_institution.php?inst_name=" + input.value;
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

function change_password() {
	var button = document.getElementById("passwordchange_button");
	var password = document.getElementById("password");
	var confirmationPassword = document.getElementById("confirm_password");
	var userSelector = document.getElementById("passwordchange_user_selector");
    
    var req = new XMLHttpRequest();
    var url = "https://" + _administrateRoot + "change_password.php";
	var postString = "userid=" + userSelector.value +"&newpass1=" + password.value + "&newpass2=" + confirmationPassword.value;
    req.onreadystatechange = function() {
        if (req.readyState==4 && req.status==200)
        {
            var response = req.responseText.split(",", 2);
			confirmationPassword.nextElementSibling.innerHTML = "";
            if(response[0] == "0") {
				password.nextElementSibling.innerHTML = " " + response[1];
                button.nextElementSibling.innerHTML = "";
				userSelector.nextElementSibling.innerHTML = "";
            } else if(response[0] == "1") {
				password.nextElementSibling.innerHTML = "";
                button.nextElementSibling.innerHTML = " " + response[1];
				userSelector.nextElementSibling.innerHTML = "";
				reload_password_change_requests();
            } else {
				password.nextElementSibling.innerHTML = "";
                button.nextElementSibling.innerHTML = "";
				userSelector.nextElementSibling.innerHTML = " " + response[1];
			}
        }
    }
	req.open("POST", url, false);
	req.setRequestHeader("Content-type","application/x-www-form-urlencoded");
    req.send(postString);
}

function reload_pending_users ()
{
    var req = new XMLHttpRequest();
    var getString = "https://" + _administrateRoot + "fragments/pending_users_table.php";
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
    var getString = "https://" + _administrateRoot + "fragments/current_faculty_table.php";
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
    var getString = "https://" + _administrateRoot + "fragments/institutions_selector.php";
    req.onreadystatechange = function () {
        if (req.readyState==4 && req.status==200)
        {
            document.getElementById("institutions_selector").innerHTML = req.responseText;
        }
    }
    req.open("GET", getString, false);
    req.send();
}

function reload_password_change_requests() {
	var req = new XMLHttpRequest();
    var getString = "https://" + _administrateRoot + "fragments/passwordchange_user_selector.php";
    req.onreadystatechange = function () {
        if (req.readyState==4 && req.status==200)
        {
            document.getElementById("passwordchange_user_selector").parentNode.innerHTML = req.responseText;
        }
    }
    req.open("GET", getString, false);
    req.send();
}

addLoadEvent(administrate_init);