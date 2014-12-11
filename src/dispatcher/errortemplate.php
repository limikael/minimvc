<html>
	<style>
		body, html {
			padding: 0;
			margin: 0;
			height: 100%;
		}

		body {
			background: #5090d0;
			height: 100%;
		}

		.content {
			background: #181090;
			position: absolute;
			top: 0; left: 0; bottom: 0; right: 0;
			height: 80%;
			width: 80%;
			margin: auto;
			color: #5090d0;
			font-family: courier;
			font-weight: bold;
			padding: 10px;
		}
	</style>
	<body>
		<div class="content">
			**** <?php echo $_SERVER["HTTP_HOST"]; ?> ****
			<br/><br/>

			<?php echo $error->getMessage(); ?>
			<br/><br/>

			<?php echo $error->getTraceAsString(); ?>
			<br/><br/>
		</div>
	</body>
</html>