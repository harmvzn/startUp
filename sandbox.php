<!DOCTYPE html>
<html>
	<head>
		<title>
			Sandbox
		</title>
	</head>
	<body style="background-color:#171717">
		<div style="background-color:#D0D0D0">
		<?php
		if (!empty($_POST['input'])) {
			eval($_POST['input']);
		}
		?>			
		</div>
		<form method="POST">
			<textarea name="input" style="width: 80%; height: 500px; background-color:#D0D0D0"><?= !empty($_POST['input']) ? $_POST['input'] : '' ?></textarea>
			<button type="submit" style="width: 100px; height: 40px;">Exc</button>
		</form>
	</body>
</html>
