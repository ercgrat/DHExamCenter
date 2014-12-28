function course_fragment_tagPresentationSelector_init() {
	document.getElementById("tag_presentation_selector").getElementsByTagName("select")[0].addEventListener("change", tagPresentationChanged, true);
}

function tagPresentationChanged() {
	var selectedValue = this.options[this.selectedIndex].value;
	reload_tags(selectedValue);
}

addLoadEvent(course_fragment_tagPresentationSelector_init);