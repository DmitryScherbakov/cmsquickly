<?php
$this->addJsFile('https://www.google.com/recaptcha/api.js');
include(TMPLS . 'header.php');
?>

<form action="" method="post">
	<input type="text" name="text">
	<input type="submit" name="send">
</form>

<?php include(TMPLS . 'footer.php') ?>