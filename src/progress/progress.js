// progress.js

function progress_init() {
	var barcharts = document.getElementsByClassName("bar_chart");
	for(var i = 0; i < barcharts.length; i++) {
		barchart = barcharts[i];
		var rects = barchart.getElementsByTagName("rect");
		for(var j = 0; j < rects.length; j++) {
			var rect = rects[j];
			rect.addEventListener("click", clickedBar, true);
			rect.addEventListener("mouseover", mousedOverBar, true);
			rect.addEventListener("mouseout", mousedOutOfBar, true);
		}
	}
	
	var donutcharts = document.getElementsByClassName("donut_chart");
	for(var i = 0; i < donutcharts.length; i++) {
		donut = donutcharts[i];
		if(donut.getAttribute('data-identifier') != "") {
			donut.parentNode.addEventListener("click", clickedDonut, true);
			donut.parentNode.style.cursor = "pointer";
		}
	}
}

function clickedBar() {
	window.location.href = "https://" + _progressQuestionUrl + "?id=" + this.getAttribute("data-identifier") + "&classid=" + this.parentNode.parentNode.getAttribute("data-identifier");
}

function clickedDonut() {
	var donut = this.getElementsByTagName("svg")[0];
	window.location.href = "https://" + _progressTagUrl + "?ids[]=" + donut.getAttribute("data-identifier");
}

function mousedOverBar() {
	this.style.cursor = "pointer";
	this.setAttribute("fill", "black");
}

function mousedOutOfBar() {
	this.style.cursor = "default";
	this.setAttribute("fill", "url(#grad1)");
}

addLoadEvent(progress_init);