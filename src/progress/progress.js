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
}

function clickedBar() {
	window.location.href = "https://" + _progressQuestionUrl + "?id=" + this.dataset.identifier;
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