<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>Login</title>
        <!-- module:rm.common.scripts -->
        <script src="/js/page.login.functions.js"></script>
        <!-- module:rm.js.init -->
        <!-- module:rm.styles -->
    </head>
    <body class="public login">
        <!-- module:rm.templates -->
                <div class="login_frame_wrap  dynTop">
                    <div id="header">
                        <div id="label">Authorization</div>
                        <a href="/"><span id="rm_logo">radiomanager</span></a>
                    </div>
                    <form method="POST" action="/login" id="loginForm">
                        <div class="loginRow">
                            <label for="login" class="loginLabel">Login:</label>
                            <input class="loginInput" id="login" type="text" value="" name="login" required />
                        </div>
                        <div class="loginRow">
                            <label for="passwd" class="loginLabel">Password:</label>
                            <input class="loginInput" id="passwd" type="password" value="" name="password" required />
                        </div>
                        <div class="loginRow">
                            <a class="subRegLink" href="/signup">Sign Up</a>
                            <input type="submit" class="loginSubmit" value="Login" />
                        </div>                            
                    </form>
                </div>
    </body>
</html>