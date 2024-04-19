<html lang="es">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover" />
	<?php wp_head(); ?>
</head>

<body class="<?php foreach (get_body_class() as $class) echo $class . ' '; ?>">
	<div class="contenedor-content container-fluid d-flex flex-column align-items-center justify-content-center">

		<?php
		if (have_posts()) :
			while (have_posts()) : the_post();
				the_content();
			endwhile;
		else :
			_e('Sorry, no posts were found.', 'textdomain');
		endif;
		?>


	</div>
	<?php wp_footer(); ?>
</body>

</html>