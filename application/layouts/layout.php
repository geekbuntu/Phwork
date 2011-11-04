<html>
	<head>
		<?php echo($this->oPage->getMetaTags()) ?>
		<title><?php echo($this->oPage->getTitle()) ?></title>
		<?php echo($this->oPage->getStylesheets()) ?>
		<?php echo($this->oPage->getJavascript()) ?>
	</head>
	<body class="<?php echo($this->oPage->getBodyClass()) ?>">
		<?php echo($this->oPage->getContent()) ?>
	</body>
</html>
