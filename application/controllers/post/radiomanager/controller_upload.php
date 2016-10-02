<?php

if(isset($_FILES['file']))
{
    echo track::uploadFile($_FILES['file']);
}
