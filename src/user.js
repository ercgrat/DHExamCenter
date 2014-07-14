function user_init() {
	document.getElementById("password_button").onclick = change_password;
}

function change_password() {
	var oldpass = document.getElementById("oldpass").value;
	var newpass1 = document.getElementById("newpass1").value;
	var newpass2 = document.getElementById("newpass2").value;
	
	var req = new XMLHttpRequest();
    var filename = "user-change_password.php";
	var postString = "oldpass=" + oldpass + "&newpass1=" + newpass1 + "&newpass2=" + newpass2;
    req.onreadystatechange = function () {
        if(req.readyState == 4 && req.status == 200)
        {
            var result = req.responseText.split(",");
			var passwordButton = document.getElementById("password_button");
			if(result[0] == "0") {
				passwordButton.nextElementSibling.innerHTML = result[1];
			} else {
				var form = passwordButton.parentElement;
				var formParent = form.parentElement;
				formParent.removeChild(form);
				formParent.innerHTML += "<p>" + result[1] + "</p>";
			}
        }
    }
	
    req.open("POST", filename, false);
	req.setRequestHeader("Content-type","application/x-www-form-urlencoded");
    req.send(postString);
	
	return false;
}

addLoadEvent(user_init);