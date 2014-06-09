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

function animate(ref, start, end, unit)
{
    var interval;
    animate.update = function (ref, start, end, unit) {
        var tween = [1,2,3,4,5,6,7,8,9,10,9,8,7,6,5,4,3,2,1];
        var distance = end - start;
        if(typeof animate.update.i == "undefined") { animate.update.i = 0; }
         
        ref += (distance * (tween[animate.update.i]/100)) + unit;
        animate.update.i++;
        
        if(animate.update.i > 18) {
            clearInterval(interval);
            animate.update.i = 0;
        }
    }
    
    interval = setInterval(animate.update.bind(ref,start,end,unit), 200);
}

//Take from http://www.javascriptkit.com/javatutors/setcss3properties.shtml
function getSupportedProperty(propertyArray){
    var root=document.documentElement //reference root element of document
    for (var i=0; i<propertyArray.length; i++){ //loop through possible properties
        if (propertyArray[i] in root.style){ //if property exists on element (value will be string, empty string if not set)
            return propertyArray[i] //return that string
        }
    }
}


addLoadEvent(layout_init);