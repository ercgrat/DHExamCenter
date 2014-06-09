var lists = new Array();

function student_init ()
{
    lists = document.getElementsByClassName("student_taglist");
    for(var i = 0; i < lists.length; i++)
    {
        var tag_items = lists[i].getElementsByClassName("tag_item");
        for(var j = 0; j < tag_items.length; j++)
        {
            tag_items[j].onclick = select_item;
            tag_items[j].style.borderColor = "#808080";
        }
    }
}

function select_item ()
{
    var dataItem = this;
    var tagid = dataItem.firstElementChild.value;
    borderColor = dataItem.style.borderColor;
    var list = dataItem.parentNode;
    var form = list.parentNode;
    
    if(borderColor == "#808080" || borderColor == "rgb(128, 128, 128)")
    {
        dataItem.style.borderColor = "#00CC00";
		var boxShadowProperty = getSupportedProperty(['boxShadow', 'MozBoxShadow', 'WebkitBoxShadow']) //get appropriate CSS3 box-shadow property
		dataItem.style[boxShadowProperty] = "1px 2px 1px #00CC00" //set CSS shadow

        var input = document.createElement("input");
        input.type = "hidden";
        input.value = tagid;
        input.name = "tags[]";
        form.lastElementChild.appendChild(input);
    }
    else
    {
        dataItem.style.borderColor = "#808080";
		var boxShadowProperty = getSupportedProperty(['boxShadow', 'MozBoxShadow', 'WebkitBoxShadow']) //get appropriate CSS3 box-shadow property
		dataItem.style[boxShadowProperty] = "1px 2px 1px #8A8A8A" //set CSS shadow
		
        var inputs = form.lastElementChild.getElementsByTagName("input");
        for(var i = 0; i < inputs.length; i++)
        {
            if(inputs[i].value == tagid)
            {
                form.lastElementChild.removeChild(inputs[i]);
                break;
            }
        }
    }
}

addLoadEvent(student_init);