function class_init()
{
    document.getElementById("student_button").onclick = invite_student;
    document.getElementById("registered_student_button").onclick = add_student;
    document.getElementById("ta_button").onclick = add_ta;
    document.getElementById("end_button").onclick = end_class;
    document.getElementById("student_form").onsubmit = function () { return false; }
    document.getElementById("registered_student_form").onsubmit = function () { return false; }
    var buttons = document.getElementsByClassName("delete_invite_button");
    for(var i = 0; i < buttons.length; i++)
    {
        buttons[i].onclick = delete_student_invite;
    }
}

function add_ta()
{
    var selector = document.getElementById("student_selector");
    var button = document.getElementById("ta_button");
    button.nextElementSibling.innerHTML = "";
    
    if(selector.value == "" || isNaN(selector.value))
    {
        selector.nextElementSibling.innerHTML = "Please select a student.";
        return;
    }
    else
    {
        selector.nextElementSibling.innerHTML = "";
    }
    
    var req = new XMLHttpRequest();
    var getString = "class-add_ta.php?id=" + selector.value;
    req.onreadystatechange = function () {
        if(req.readyState == 4 && req.status == 200)
        {
            if(req.responseText == "one")
            {
                button.nextElementSibling.innerHTML = selector.options[selector.selectedIndex].innerHTML + " has been promoted to a teaching assistant!";
                reload_students();
            }
            else
            {
                button.nextElementSibling.innerHTML = "An error occurred. Please contact the site administrator.";
            }
        }
    }
    req.open("GET", getString, false);
    req.send();
}

function add_student()
{
    var input = document.getElementById("registered_student_selector");
    var button = document.getElementById("registered_student_button");
    
    if(input.value == "" || isNaN(input.value))
    {
        input.nextElementSibling.innerHTML = "Please select a student to add.";
        button.nextElementSibling.innerHTML = "";
        return;
    }
    
    var req = new XMLHttpRequest();
    var getString = "class-add_student.php?id=" + input.value;
    req.onreadystatechange = function () {
        if(req.readyState == 4 && req.status == 200)
        {
            var response = req.responseText.split("|",2);
            if(response[0] == "0")
            {
                input.nextElementSibling.innerHTML = response[1];
                button.nextElementSibling.innerHTML = "";
            }
            else
            {
                input.nextElementSibling.innerHTML = "";
                button.nextElementSibling.innerHTML = response[1];
                if(response[0] == "2")
                {
                    reload_students();
					reload_student_selector();
                    reload_registered_student_selector();
                }
            }
        }
    }
    req.open("GET", getString, false);
    req.send();
}

function invite_student()
{
    var input = document.getElementById("student_account_name");
    var button = document.getElementById("student_button");
    
    if(input.value == "" || input.value.length < 4 || input.value.length > 32)
    {
        input.nextElementSibling.innerHTML = "The username must be between 4 and 32 characters in length.";
        button.nextElementSibling.innerHTML = "";
        return;
    }
    
    var req = new XMLHttpRequest();
    var getString = "class-invite_student.php?account=" + input.value;
    req.onreadystatechange = function () {
        if(req.readyState == 4 && req.status == 200)
        {
            var response = req.responseText.split("|", 2);
            if(response[0] == "0")
            {
                input.nextElementSibling.innerHTML = response[1];
                button.nextElementSibling.innerHTML = "";
            }
            else
            {
                input.nextElementSibling.innerHTML = "";
                button.nextElementSibling.innerHTML = response[1];
                if(response[0] == "2")
                {
                    reload_pending_students();
                    class_init();
                }
            }
        }
    }
    req.open("GET", getString, false);
    req.send();
    
}

function reload_pending_students()
{
    var req = new XMLHttpRequest();
    var getString = "class-fragment-pending_student_table.php";
    req.onreadystatechange = function () {
        if (req.readyState == 4 && req.status == 200)
        {
            document.getElementById("pending_student_table").innerHTML = req.responseText;
            class_init();
        }
    }
    req.open("GET", getString, false);
    req.send();
}

function reload_registered_student_selector()
{
    var req = new XMLHttpRequest();
    var getString = "class-fragment-registered_student_selector.php";
    req.onreadystatechange = function () {
        if(req.readyState == 4 && req.status == 200)
        {
            var selector = document.getElementById("registered_student_selector");
            var parent = selector.parentNode;
            parent.innerHTML = req.responseText + "<span class='warning'/>";
        }
    }
    req.open("GET", getString, false);
    req.send();
}

function reload_students()
{
    var req = new XMLHttpRequest();
    var getString = "class-fragment-student_table.php";
    req.onreadystatechange = function () {
        if(req.readyState == 4 && req.status == 200)
        {
            document.getElementById("student_table").innerHTML = req.responseText;
        }
    }
    req.open("GET", getString, false);
    req.send();
}

function reload_student_selector() {
	var req = new XMLHttpRequest();
    var getString = "class-fragment-student_selector.php";
    req.onreadystatechange = function () {
        if(req.readyState == 4 && req.status == 200)
        {
            var selector = document.getElementById("student_selector");
            var parent = selector.parentNode;
            parent.innerHTML = req.responseText + "<span class='warning'/>";
        }
    }
    req.open("GET", getString, false);
    req.send();
}

function delete_student_invite()
{
    var account = this.parentNode.previousElementSibling.innerHTML;
    var req = new XMLHttpRequest();
    var getString = "class-delete_student_invite.php?account=" + account;
    req.onreadystatechange = function () {
        if (req.readyState == 4 && req.status == 200)
        {
            reload_pending_students();
        }
    }
    req.open("GET", getString, false);
    req.send();
}

function end_class()
{
    var selection = confirm("Are you sure you want to terminate this class?");
    if(selection == true)
    {
        var req = new XMLHttpRequest();
        var getString = "class-end.php";
        req.onreadystatechange = function () {
            if(req.readyState == 4 && req.status == 200)
            {
                window.location.assign("https://xlearn.obdurodon.org/instructor.php");
            }
        }
        req.open("GET", getString, false);
        req.send();
    }
}

addLoadEvent(class_init);



















