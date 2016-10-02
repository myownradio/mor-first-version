function clientLogin(login, password)
{
    $.post("/login", {login: login, password: password}, function(data) {
        console.log(data);
    });
}

function clientLogout()
{
    $.post("/logout", {
        authtoken : mor.user_token
    }, function(data) {
        try
        {
            var json = JSON.parse(data);
            if (json.code !== undefined)
            {
                if (json.code === 'LOGOUT_SUCCESS')
                {
                    location.reload();
                }
                else
                {
                    myMessageBox(langMessages.user[json.code]);
                }
            }
        }
        catch (e)
        {

        }

    });
}


$(".linkLogout").livequery("click", function() {
    clientLogout();
});
