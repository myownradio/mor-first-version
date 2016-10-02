<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>Registration on myownradio.biz</title>
        <!-- module:rm.scripts -->
        <!-- module:rm.js.init -->
        <!-- module:rm.styles -->
    </head>
    <body class="public login">
        <!-- module:rm.templates -->
                <div class="login_frame_wrap  dynTop">
                    <div id="header">
                        <div id="label">Sign Up</div>
                        <a href="/"><span id="rm_logo">radiomanager</span></a>
                    </div>
                    <form method="POST" action="/signup" id="signupForm">
                        <div class="loginRow"><div class="loginHint">To continue registration please enter your email.<br>Instructions for registration will be sent to it.</div></div>
                        <div class="loginRow">
                            <label for="email" class="loginLabel">Email:</label>
                            <input class="loginInput" id="email" type="text" value="" name="email" required />
                        </div>
                        <div class="loginRow">
                            <input type="submit" class="loginSubmit" value="Send Email" />
                        </div>                            
                    </form>
                </div>
    </body>
</html>