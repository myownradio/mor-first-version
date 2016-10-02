<?php

if(user::getCurrentUserId() == 0)
{
    echo application::getModule("page.us.login");
}
else
{
    header("HTTP/1.1 302 Moved Temporarily");
    header("Location: /radiomanager");
}
