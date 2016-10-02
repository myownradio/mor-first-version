<?php $dir = opendir("js"); 
while($file = readdir($dir)): 
if(preg_match("/^\d+\.mod\..+\.js$/", $file)): 
?>
<script src="/js/<?= $file ?>"></script>
<?php   
endif; 
endwhile; 
closedir($dir);