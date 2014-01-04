// layout.js
// Eric Gratta

//addLoadEvent(func) written by Simon Willison.  Taken from htmlgoodies.com.
function addLoadEvent(func) {
  var oldonload = window.onload;
  if (typeof window.onload != 'function') {
    window.onload = func;
  } else {
    window.onload = function() {
      if (oldonload) {
        oldonload();
      }
      func();
    }
  }
}

function layout_init()
{
    var expander_heads = document.getElementsByClassName("expander_head");
    for(var i = 0; i < expander_heads.length; i++)
    {
        expander_heads[i].onclick = expand;
    }
}

function expand()
{
    if(this.nextElementSibling.style.display == "block")
    {
        this.nextElementSibling.style.display = "none";
        var toggle_up = this.getElementsByClassName("toggle_up")[0];
        toggle_up.style.display = "none";
        var toggle_down = this.getElementsByClassName("toggle_down")[0];
        toggle_down.style.display = "inline-block";
    }
    else
    {
        this.nextElementSibling.style.display = "block";
        var toggle_up = this.getElementsByClassName("toggle_up")[0];
        toggle_up.style.display = "inline-block";
        var toggle_down = this.getElementsByClassName("toggle_down")[0];
        toggle_down.style.display = "none";
    }
}

addLoadEvent(layout_init);