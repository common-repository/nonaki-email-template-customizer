<?php
defined('ABSPATH') || exit;

?>
<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">

	<style>
		.nonaki-preview-div {
			height: 100px;
			width: 160px;
			display: flex;
			justify-content: center;
			position: fixed;
			right: 20px;
			bottom: 0px;
			align-items: center;
		}

		.nonaki-preview-btn {
			display: flex;
			align-items: center;
			color: white;
			background: #242B3B;
			padding: 5px 10px;
			border-radius: 5px;
			text-decoration: none;
			gap: 4px;
			font-family: sans-serif;
		}

		.nonaki-svg {
			width: 30px
		}
	</style>
</head>

<body>
	<?php
	if ($nonaki_content_type === 'mail') {
		echo nonaki_e($nonaki_compiled_content);
	} else {
		echo nonaki_e($nonaki_content);
	} ?>

</body>

</html>