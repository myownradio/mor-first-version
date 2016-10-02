<?php 
$dir = opendir("css");
while($file = readdir($dir)): 
    if(preg_match("/^\d+\.mod\..+\.css$/", $file)): ?>
<link rel="stylesheet" type="text/css" href="/css/<?= $file ?>" />
<?php   
    endif; 
endwhile; 
closedir($dir); 
