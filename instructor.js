function instructor_init()
{
    document.getElementById("course_button").onclick = create_course;
    document.getElementById("class_button").onclick = create_class;
}

function create_class ()
{
    var course_input = document.getElementById("course_selector");
    var class_input = document.getElementById("class_input");
    var input = document.getElementById("class_button");
    
    if(course_input.value == "")
    {
        course_input.nextElementSibling.innerHTML = "Please select a course, or create a course if you have not yet done so.";
        return;
    }
    else
    {
        course_input.nextElementSibling.innerHTML = "";
    }
    
    if(class_input.value == "" || class_input.value.length > 256)
    {
        class_input.nextElementSibling.innerHTML = "Enter a name between 1 and 256 characters.";
    }
    else
    {
        class_input.nextElementSibling.innerHTML = "";
    }
    
    var req = new XMLHttpRequest();
    var getString = "instructor-create_class.php?course_id=" + course_input.value + "&class_title=" + class_input.value;
    req.onreadystatechange = function() {
        if (req.readyState == 4 && req.status == 200)
        {
            var response = req.responseText.split("|", 2);
            if(response[0] == "0")
            {
                input.nextElementSibling.innerHTML = response[1];
                document.getElementById("class_warning").innerHTML = "";
                class_input.nextElementSibling.innerHTML = "";
            }
            else if (response[0] == "1")
            {
                input.nextElementSibling.innerHTML = "";
                document.getElementById("class_warning").innerHTML = response[1];
                class_input.nextElementSibling.innerHTML = "";
                class_input.value = "";
                
                reload_courses_table();
            }
            else
            {
                input.nextElementSibling.innerHTML = "";
                document.getElementById("class_warning").innerHTML = "";
                class_input.nextElementSibling.innerHTML = response[1];
            }
        } else {
            document.getElementById("class_warning").innerHTML = "An error occurred.";
        }
    }
    req.open("GET", getString, false);
    req.send();
}

function create_course ()
{
    var institution = document.getElementById("institution").innerHTML;
    var input = document.getElementById("course_input");
    if(input.value == "" || input.value.length > 256)
    {
        input.nextElementSibling.innerHTML = "Enter a name between 1 and 256 characters.";
        return;
    }
    
    var req = new XMLHttpRequest();
    var getString = "instructor-create_course.php?course_title=" + input.value + "&inst_id=" + institution;
    req.onreadystatechange = function() {
        if (req.readyState==4 && req.status==200)
        {
            var response = req.responseText.split("|", 2);
            if(response[0] == "0")
            {
                input.nextElementSibling.innerHTML = response[1];
                document.getElementById("course_warning").innerHTML = "";
            }
            else
            {
                input.nextElementSibling.innerHTML = "";
                input.value = "";
                document.getElementById("course_warning").innerHTML = response[1];
                reload_courses_table();
                reload_courses_selector();
            }
        }
    }
    req.open("GET", getString, false);
    req.send();
}

function reload_courses_table ()
{
    var req = new XMLHttpRequest();
    var getString = "instructor-fragment-course_table.php";
    req.onreadystatechange = function () {
        if (req.readyState==4 && req.status==200)
        {
            document.getElementById("courses_table").innerHTML = req.responseText;
        }
    }
    req.open("GET", getString, false);
    req.send();
}

function reload_courses_selector ()
{
    var req = new XMLHttpRequest();
    var getString = "instructor-fragment-course_selector.php";
    req.onreadystatechange = function () {
        if (req.readyState==4 && req.status==200)
        {
            document.getElementById("course_selector_container").innerHTML = req.responseText;
        }
    }
    req.open("GET", getString, false);
    req.send();
}

addLoadEvent(instructor_init);