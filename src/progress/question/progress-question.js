// progress-question.js
function progress_question_init() {
	var edit_button = document.getElementById("edit_button");
	edit_button.addEventListener("click", editButtonClicked, true);
	
	var delete_button = document.getElementById("delete_button");
	delete_button.addEventListener("click", deleteButtonClicked, true);
	
	var tag_button = document.getElementById("tag_button");
	tag_button.addEventListener("click", tagButtonClicked, true);
}

function editButtonClicked() {
	alert("https://" + _progressQuestionEditUrl + "?id=" + this.dataset.identifier);
	window.location.href = "https://" + _progressQuestionEditUrl + "?id=" + this.dataset.identifier;
}

function deleteButtonClicked() {
	var confirmation = confirm("Are you sure you want to delete this question?");
	if(confirmation) {
		var questionid = this.dataset.identifier;
		if(isNaN(questionid)) { return; }
    
		var getString = "https://" + _progressQuestionDeleteUrl + "?id=" + questionid;
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

function tagButtonClicked() {
	window.location.href = "https://" + _progressQuestionTagUrl + "?id=" + this.dataset.identifier;
}

addLoadEvent(progress_question_init);