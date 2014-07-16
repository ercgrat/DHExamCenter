function user_password_reset_init() {
	document.getElementById("reset_button").addEventListener("click", resetPassword, true);
}

function resetPassword() {
	var button = document.getElementById("reset_button");
	var username = document.getElementById("username");
	var req = new XMLHttpRequest();
	var filename = "user-request_password_reset.php";
    var postString = "username=" + username.value;
    req.onreadystatechange = function () {
        if(req.readyState == 4 && req.status == 200)
        {
			var response = req.responseText.split(",", 2);
            if(response[0] == "1")
            {
                button.nextElementSibling.innerHTML = " " + response[1];
                username.nextElementSibling.innerHTML = "";
            }
            else
            {
                button.nextElementSibling.innerHTML = "";
                username.nextElementSibling.innerHTML = " " + response[1];
            }
        }
    }
	req.open("POST", filename, false);
	req.setRequestHeader("Content-type","application/x-www-form-urlencoded");
    req.send(postString);
}

addLoadEvent(user_password_reset_init);