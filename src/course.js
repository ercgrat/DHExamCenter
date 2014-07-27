function course_init ()
{
    document.getElementById("resource_button").addEventListener("click", add_resource, true);
	document.getElementById("tags_button").addEventListener("click", searchQuestions, true);
}

function searchQuestions() {

	var tagItems = document.getElementById("tags_table").getElementsByClassName("tag_item");
	var tagIdentifiers = new Array();
	for(var i = 0; i < tagItems.length; i++) {
		if(tagItems[i].dataset.selected == "1") {
			tagIdentifiers.push(tagItems[i].dataset.tagid);
		}
	}
	// Construct form to submit data while redirecting
	var tagIdentifierString = tagIdentifiers.join(',');
	var formString = "<form method='POST' action='tag.php'><input type='hidden' name='tagids' value='" + tagIdentifierString + "'></input></form>";
	var formDiv = document.createElement("div");
	formDiv.innerHTML = formString;
	document.body.appendChild(formDiv);
	formDiv.firstChild.submit();
    
}

function reload_resources()
{
    var req = new XMLHttpRequest();
    var getString = "course-fragment-resource_selector.php";
    req.onreadystatechange = function () {
        if(req.readyState == 4 && req.status == 200)
        {
            document.getElementById("resource_selector").innerHTML = req.responseText;
        }
    }
    req.open("GET", getString, false);
    req.send();
}

function reload_tags()
{
    var req = new XMLHttpRequest();
    var getString = "course-fragment-tags_table.php";
    req.onreadystatechange = function () {
        if(req.readyState == 4 && req.status == 200)
        {
            document.getElementById("tags_table").innerHTML = req.responseText;
        }
    }
    req.open("GET", getString, false);
    req.send();
}

function add_resource()
{
    document.getElementById("resource_button").nextElementSibling.innerHTML = "";
    var name_input = document.getElementById("resource_name");
    var url_input = document.getElementById("resource_url");
    
    var input_valid = true;
    if(name_input.value == "")
    {
        name_input.nextElementSibling.innerHTML = "Please enter a resource name.";
        input_valid = false;
    }
    else
    {
        name_input.nextElementSibling.innerHTML = "";
    }
    
    if(url_input.value == "")
    {
        url_input.nextElementSibling.innerHTML = "Please enter a link to the resource.";
        input_valid = false;
    }
    else
    {
        url_input.nextElementSibling.innerHTML = "";
    }
    
    if(input_valid)
    {
        var req = new XMLHttpRequest();
        var getString = "course-add_resource.php?name=" + encodeURIComponent(name_input.value) + "&link=" + encodeURIComponent(url_input.value);
        req.onreadystatechange = function () {
            if(req.readyState == 4 && req.status == 200)
            {
                name_input.value = "";
                url_input.value = "http://";
                document.getElementById("resource_button").nextElementSibling.innerHTML = "Resource added successfully!";
                reload_resources();
            }
        }
        req.open("GET", getString, false);
        req.send();
    }
}

addLoadEvent(course_init);