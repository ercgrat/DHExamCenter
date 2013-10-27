var tables = new Array();

function student_init ()
{
    tables = document.getElementsByTagName("table");
    for(var i = 0; i < tables.length; i++)
    {
        var tag_items = tables[i].getElementsByClassName("tag_item");
        for(var j = 0; j < tag_items.length; j++)
        {
            tag_items[j].onclick = select_item;
        }
    }
}

function select_item ()
{
    var data_cell = this;
    var tagid = data_cell.firstElementChild.value;
    var borderColor = window.getComputedStyle(data_cell).getPropertyValue('border-color');
    var table = this.parentNode.parentNode.parentNode;
    var form = table.parentNode;
    
    if(borderColor == "rgb(128, 128, 128)")
    {
        data_cell.style.borderColor = "#3FFF00";
        var input = document.createElement("input");
        input.type = "hidden";
        input.value = tagid;
        input.name = "tags[]";
        form.lastElementChild.appendChild(input);
    }
    else
    {
        data_cell.style.borderColor = "#808080";
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