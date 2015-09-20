function tag_init()
{
    var questions = document.getElementsByClassName("review_question");
    for(var i = 0; i < questions.length; i++)
    {
        var tags = questions[i].getElementsByTagName("a");
        for (var j = 0; j < tags.length; j++)
        {
            tags[j].onmouseover = deselect_question;
            tags[j].onmouseout = select_question;
        }
    }
    
    var delete_buttons = document.getElementsByClassName("delete");
    for(var i = 0; i < delete_buttons.length; i++)
    {
        delete_buttons[i].onmouseover = deselect_question;
        delete_buttons[i].onmouseout = select_question;
        delete_buttons[i].onclick = delete_question;
    }
}

function delete_question (event)
{
    event.stopPropagation();
	var confirmation = confirm("Are you sure you want to delete this question?");
	if(confirmation) {
		var questionid = this.previousElementSibling.value;
		if(isNaN(questionid)) { return; }
		
		var getString = "tag-delete_question.php?id=" + questionid;
		var req = new XMLHttpRequest();
		req.onreadystatechange = function () {
			if(req.readyState == 4 && req.status == 200)
			{
				location.reload();
			}
		}
		req.open("GET", getString, false);
		req.send();
	}
}

function deselect_question ()
{
    if(this.tagName == "A")
    {
        var question = this.parentNode.parentNode.parentNode;
    }
    else
    {
        var question = this.parentNode;
    }
    question.className = question.className.replace(/ hoverable/, "");
}

function select_question ()
{
	if(this.tagName == "A")
    {
        var question = this.parentNode.parentNode.parentNode;
    }
    else
    {
        var question = this.parentNode;
    }
    question.className += " hoverable";
}

addLoadEvent(tag_init);