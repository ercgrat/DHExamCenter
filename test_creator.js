
function init()
{
    var a_adders = document.getElementsByClassName("a_adder");
    for(var i = 0; i < a_adders.length; i++)
    {
        a_adders[i].onmousedown = press;
        a_adders[i].onmouseup = a_add;
    }
    var a_removers = document.getElementsByClassName("a_remover");
    for(var i = 0; i < a_removers.length; i++)
    {
        a_removers[i].onmousedown = press;
        a_removers[i].onmouseup = a_remove;
    }
    
    var q_adder = document.getElementsByClassName("q_adder")[0];
    q_adder.onmousedown = press;
    q_adder.onmouseup = insert_question_block;
    var q_remover = document.getElementsByClassName("q_remover")[0];
    q_remover.onmousedown = press;
    q_remover.onmouseup = remove_question_block;
}

function validate_input()
{
    var previous_warnings = document.getElementsByClassName("warning");
    for(var i = previous_warnings.length-1; i >= 0; i--)
    {
        var parent = previous_warnings[i].parentNode;
        parent.removeChild(previous_warnings[i]);
    }

    var input_elements = document.getElementsByTagName("input");
    var submit = true;
    for(var i = 0; i < input_elements.length; i++)
    {
        if(input_elements[i].type == "text")
        {
            if(input_elements[i].value == "")
            {
                var parent_label = input_elements[i].parentNode;
                var msg = document.createElement("span");
                var msg_text = document.createTextNode("Please enter a value.");
                msg.appendChild(msg_text);
                msg.style.color = "red";
                msg.className = "warning";
                parent_label.appendChild(msg);
                submit = false;
            }
        }
    }
    
    var questions = document.getElementsByClassName("answer_list");
    for(var i = 0; i < questions.length; i++)
    {
        var answers = questions[i].getElementsByTagName("input");
        var has_correct = false;
        for(var j = 0; j < answers.length; j++)
        {
            if(answers[j].type == "checkbox")
            {
                if(answers[j].checked)
                {
                    has_correct = true;
                }
            }
        }
        if(!has_correct)
        {
            submit = false;
            var msg = document.createElement("span");
            var msg_text = document.createTextNode("  Please specify at least one correct answer.");
            msg.appendChild(msg_text);
            msg.style.color = "red";
            msg.style.fontSize = "80%";
            msg.className = "warning";
            var previous_header = questions[i].previousElementSibling;
            previous_header.appendChild(msg);
        }
    }
    return submit;
}

function a_add()
{
    var answer_list = this.previousElementSibling;
    var answer_fields = answer_list.getElementsByClassName("answer");
    var num_answers = answer_fields.length;
    
    var new_checkbox = document.createElement("input");
    new_checkbox.type = "checkbox";
    new_checkbox.name = "correct[]";
    new_checkbox.value = num_answers + 1;
    
    var new_label = document.createElement("label");
    var label_text = document.createTextNode(" Answer " + (num_answers+1) + ": ");
    new_label.appendChild(label_text);
    var new_answer = document.createElement("input");
    new_answer.type = "text";
    new_answer.className = "answer";
    new_answer.name = "answers";
    new_answer.size = "100";
    new_label.appendChild(new_answer);
    
    var new_br = document.createElement("br");
    
    var new_text = document.createTextNode(" ");
    
    answer_list.appendChild(new_checkbox);
    answer_list.appendChild(new_text);
    answer_list.appendChild(new_label);
    answer_list.appendChild(new_br);
    
    release(this);
}

function a_remove()
{
    var answer_list = this.previousElementSibling.previousElementSibling;
    if(answer_list.childElementCount > 6)
    {
        var last_br = answer_list.lastElementChild;
        var last_label = last_br.previousElementSibling;
        var last_checkbox = last_label.previousElementSibling;
        
        answer_list.removeChild(last_label);
        answer_list.removeChild(last_checkbox);
        answer_list.removeChild(last_br);
    }
    else
    {
        alert("Question must have at least two answers!");
    }
    release(this);
}

function insert_question_block()
{
    var question_no = document.getElementsByClassName("answer_list").length + 1;

    var new_div_head = document.createElement("div");
    new_div_head.className = "question_head";
    var new_head = document.createElement("h3");
    var new_head_text = document.createTextNode("Question " + question_no + ":");
    new_head.appendChild(new_head_text);
    var new_head_input = document.createElement("input");
    new_head_input.className = "question";
    new_head_input.type = "text";
    new_head_input.name = "questions[]";
    new_head_input.size = "100";
    new_div_head.appendChild(new_head);
    new_div_head.appendChild(new_head_input);
    
    var new_div = document.createElement("div");
    new_div.className = "answer_list";
    for(var i = 1; i <= 4; i++)
    {
        var new_input = document.createElement("input");
        new_input.type = "checkbox";
        new_input.name = "q" + question_no + "_correct[]";
        new_input.value = i;
        
        var new_buffer = document.createTextNode(" ");
        
        var new_label = document.createElement("label");
        var new_label_text = document.createTextNode("Answer " + i + ": ");
        var new_label_input = document.createElement("input");
        new_label_input.className = "answer";
        new_label_input.type = "text";
        new_label_input.name = "q" + question_no + "_answers[]";
        new_label_input.size = "100";
        new_label.appendChild(new_label_text);
        new_label.appendChild(new_label_input);
        
        var new_br = document.createElement("br");
        
        new_div.appendChild(new_input);
        new_div.appendChild(new_buffer);
        new_div.appendChild(new_label);
        new_div.appendChild(new_br);
    }
    
    var new_add_image = document.createElement("img");
    new_add_image.src = "add.png";
    new_add_image.className = "a_adder";
    new_add_image.alt = "[ADD ANSWER]";
    new_add_image.onmousedown = press;
    new_add_image.onmouseup = a_add;
    
    var new_remove_image = document.createElement("img");
    new_remove_image.src = "minus.png";
    new_remove_image.className = "a_remover";
    new_remove_image.alt = "[REMOVE ANSWER]";
    new_remove_image.onmousedown = press;
    new_remove_image.onmouseup = a_remove;
    
    var location = document.getElementById("questions");
    location.appendChild(new_div_head);
    location.appendChild(new_div);
    location.appendChild(new_add_image);
    location.appendChild(new_remove_image);
    
    release(this);
}

function remove_question_block()
{
    var location = document.getElementById("questions");
    var answer_lists = location.getElementsByClassName("answer_list");
    if(answer_lists.length > 1)
    {
        var minus = location.lastElementChild;
        var add = minus.previousElementSibling;
        var answers = add.previousElementSibling;
        var head = answers.previousElementSibling;
        
        location.removeChild(minus);
        location.removeChild(add);
        location.removeChild(answers);
        location.removeChild(head);
    }
    else
    {
        alert("Test must have at least one question!");
    }
    
    release(this);
}

function press()
{
    var className = this.className;
    var image = "";
    switch (className) {
        case "a_adder":
        case "q_adder":
            image = "add_pressed.png";
            break;
        default:
            image = "minus_pressed.png";
            break;
    }
    this.src = image;
}

function release(button)
{
    var className = button.className;
    var image = "";
    switch (className) {
        case "a_adder":
        case "q_adder":
            image = "add.png";
            break;
        default:
            image = "minus.png";
            break;
    }
    button.src = image;
}

window.onload = init;