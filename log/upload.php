<?php
print_r($_FILES);
?>
<html>
<form action="" method="post" enctype="multipart/form-data">
<p>File:
<input type="file" name="upfile" />
<input type="submit" value="Send" />
</p>
</form>
</html>