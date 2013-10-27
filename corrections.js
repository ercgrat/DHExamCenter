function evaluate()
{
    var wrong_nodes = document.getElementsByClassName("incorrect");
    for(var i = 0; i < wrong_nodes.length; i++)
    {
        var incorrect_img = document.createElement("img");
        incorrect_img.src = "incorrect.png";
        incorrect_img.alt = "WRONG: ";
        insert(incorrect_img, wrong_nodes[i]);
    }
    var right_nodes = document.getElementsByClassName("correct");
    for(var i = 0; i < right_nodes.length; i++)
    {
        var correct_img = document.createElement("img");
        correct_img.src = "correct.png";
        correct_img.alt = "RIGHT: ";
        insert(correct_img, right_nodes[i]);
    }
}

function insert(imgNode, location)
{
    if(location.tagName == "UL")
    {
        location = location.previousElementSibling;
        location = location.childNodes[0];
        var parent = location.parentNode;
        parent.insertBefore(imgNode, location);
    }
    else
    {
        location.appendChild(imgNode);
    }
}

addLoadEvent(evaluate);