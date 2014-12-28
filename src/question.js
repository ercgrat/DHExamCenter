function question_init()
{
    var adder = document.getElementById("adder");
    adder.onclick = add_answer;
    adder.onmousedown = press;
    adder.onmouseout = release;
    adder.onmouseup = release;
    
    var remover = document.getElementById("remover");
    remover.onclick = remove_answer;
    remover.onmousedown = press;
    remover.onmouseout = release;
    remover.onmouseup = release;
    
    document.getElementById("question_button").onclick = add_question;
	if(document.getElementById("edit_button")) {
		document.getElementById("edit_button").onclick = edit_question;
	}
}

function edit_question() {
	document.getElementById("preview_view").style.display = "none";
	document.getElementById("edit_view").style.display = "block";
}

function add_question()
{
    var input_valid = true;
    var question_input = document.getElementById("question_textarea").value;
    if(question_input == "")
    {
        input_valid = false;
        document.getElementById("question_textarea").previousElementSibling.innerHTML = "Question field must not be empty.";
    }
    else
    {
        question_input = "question=" + encodeURIComponent(question_input.replace(/^\s+|\s+$/g, '')) + "&";
        document.getElementById("question_textarea").previousElementSibling.innerHTML = "";
    }
    
    var tags = "";
    var tag_input = document.getElementById("tag_textarea").value;
    if(tag_input != "")
    {
        tag_input = tag_input.replace(/^\s+|\s+$/g, '');
        if(tag_input != "")
        {
            var tag_inputs = tag_input.split(",");
            for(var i = 0; i < tag_inputs.length; i++)
            {
                var tag = tag_inputs[i];
                if(typeof tag === 'undefined' || tag == "")
                {
                    continue;
                }
                tag = encodeURIComponent(tag.replace(/^\s+|\s+$/g, ''));
                tags += ("tags[]=" + tag);
                tags += "&";
            }
            
            document.getElementById("tag_textarea").previousElementSibling.innerHTML = "";
        }
        else
        {
            input_valid = false;
            document.getElementById("tag_textarea").previousElementSibling.innerHTML = "There must be at least one tag on each question.";
        }
    }
    else
    {
        input_valid = false;
        document.getElementById("tag_textarea").previousElementSibling.innerHTML = "There must be at least one tag on each question.";
    }
    
    var answers = "";
    var answer_inputs = document.getElementsByClassName("answer");
    var truth_values = "";
    var answer_count = 0;
    var correct_count = 0;
    for(var i = 0; i < answer_inputs.length; i++)
    {
        var answer = answer_inputs[i].value;
        if(answer == "")
        {
            if(i == 0 || i == 1)
            {
                input_valid = false;
                document.getElementById("answer_warning").innerHTML = "There must be at least two answers (in the first two answer fields). ";
                break;
            }
            else
            {
                document.getElementById("answer_warning").innerHTML = "";
            }
            continue;
        }
        
        answer_count++;
        answer = encodeURIComponent(answer.replace(/^\s+|\s+$/g, ''));
        answers += ("answers[]=" + answer + "&");
        
        truth_value = answer_inputs[i].parentNode.previousElementSibling.previousElementSibling.firstElementChild.checked;
        if(truth_value)
        {
            truth_values += "truth_values[]=1&";
            correct_count++;
        }
        else
        {
            truth_values += "truth_values[]=0&";
        }
    }
    if(answer_count >= 2 && correct_count == 0)
    {
        document.getElementById("answer_warning").innerHTML += "At least one answer must be marked as correct (checked).";
        input_valid = false;
    }
    
    var explanation_input = document.getElementById("explanation_textarea").value;
    if(explanation_input != "")
    {
        explanation_input = "explanation=" + encodeURIComponent(explanation_input.replace(/^\s+|\s+$/g, '')) + "&";
    }
    
    var resource_input = document.getElementById("resource_selector").firstElementChild.value;
    if(resource_input == "")
    {
        resource_input = "";
    }
    else if (isNaN(resource_input))
    {
        input_valid = false;
    }
    else
    {
        resource_input = "resource=" + resource_input + "&";
    }
    
    var order_input = document.getElementById("order_checkbox").checked;
    if(order_input)
    {
        order_input = "order=1";
    }
    else
    {
        order_input = "order=0";
    }
    
    if(input_valid)
    {
        var getString = "course-add_question.php?" + question_input + tags + answers + truth_values + explanation_input + resource_input + order_input;
        var req = new XMLHttpRequest();
        req.onreadystatechange = function() {
            if(req.readyState == 4 && req.status == 200)
            {
                var warnings = document.getElementsByClassName("warning");
                for(var i = 0; i < warnings.length; i++)
                {
                    warnings[i].innerHTML = "";
                }
                
                if(document.getElementById("tags_table"))
                { 
                    document.getElementById("question_textarea").value = "";
                    document.getElementById("tag_textarea").value = "";
                    document.getElementById("resource_selector").firstElementChild.selectedIndex = 0;
                    document.getElementById("order_checkbox").checked = false;
                    var checkboxes = document.getElementsByTagName("input");
                    for(var i = 0; i < checkboxes.length; i++)
                    {
                        if(checkboxes[i].type == "checkbox")
                        {
                            checkboxes[i].checked = false;
                        }
                    }
                    var answers = document.getElementsByClassName("answer");
                    for(var i = 0; i < answers.length; i++)
                    {
                        answers[i].value = "";
                    }
                    document.getElementById("explanation_textarea").value = "";
                    document.getElementById("question_warning").innerHTML = "";
                    document.getElementById("question_button").nextElementSibling.innerHTML = "The question was successfully submitted!";
                    
                    reload_tags();
                }
                else
                {
                    document.getElementById("edit_view").style.display = "none";
					load_preview();
					document.getElementById("preview_view").style.display = "block";
                }
            }
        }
        req.open("GET", getString, false);
        req.send();
    }
    else
    {
        document.getElementById("question_warning").innerHTML = "Please correct the following issues:";
        document.getElementById("question_button").nextElementSibling.innerHTML = "";
    }
}

function load_preview() {
	var req = new XMLHttpRequest();
	req.onreadystatechange = function() {
		if(req.readyState == 4 && req.status == 200) {
			document.getElementById("preview_view").getElementsByTagName("div")[0].innerHTML = req.responseText;
		}
	}
	req.open("GET", "question-fragment-preview.php", false);
	req.send();
}

function add_answer()
{
    var answer_list = document.getElementById("answer_list");
    var answer_fields = answer_list.getElementsByClassName("answer");
    var num_answers = answer_fields.length;
    
    var new_table_row = document.createElement("tr");
    
    var new_checkbox_td = document.createElement("td");
    var new_checkbox = document.createElement("input");
    new_checkbox.type = "checkbox";
    new_checkbox.value = num_answers + 1;
    new_checkbox_td.appendChild(new_checkbox);
    
    var new_answer_td = document.createElement("td");
    var answer_text = document.createTextNode(" Answer " + (num_answers+1) + ": ");
    new_answer_td.appendChild(answer_text);
    
    var new_answertext_td = document.createElement("td");
    var new_answer = document.createElement("textarea");
    new_answer.className = "answer";
    new_answertext_td.appendChild(new_answer);
    
    new_table_row.appendChild(new_checkbox_td);
    new_table_row.appendChild(new_answer_td);
    new_table_row.appendChild(new_answertext_td);
    
    answer_list.firstElementChild.firstElementChild.appendChild(new_table_row);
    
    document.getElementById("question_warning").innerHTML = "";
}

function remove_answer()
{
    var answer_table = document.getElementById("answer_list").firstElementChild.firstElementChild;
    var rows = answer_table.getElementsByTagName("tr");
    if(rows.length > 2)
    {
        answer_table.removeChild(answer_table.lastElementChild);
    }
    else
    {
        alert("Question must have at least two answers!");
    }
}

function press()
{
    var id = this.id;
    var image = "";
    switch (id) {
        case "adder":
            image = "add_pressed.png";
            break;
        default:
            image = "minus_pressed.png";
            break;
    }
    this.src = image;
}

function release()
{
    var id = this.id;
    var image = "";
    switch (id) {
        case "adder":
            image = "add.png";
            break;
        default:
            image = "minus.png";
            break;
    }
    this.src = image;
}

addLoadEvent(question_init);