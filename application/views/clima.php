<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="shortcut icon" href="<?php echo site_url('images/favicon.ico')?>" />
	<link rel="stylesheet" href="<?php echo site_url('css/style.css')?>">
	<link type="text/css" rel="stylesheet" href="<?php echo site_url('css/menu.css'); ?>" />
<?php foreach($gc->css_files as $file): ?>
	<link type="text/css" rel="stylesheet" href="<?php echo $file; ?>" />
<?php endforeach; ?>
<?php foreach($gc->js_files as $file): ?>
	<script src="<?php echo $file; ?>"></script>
<?php endforeach; ?>
	<style type='text/css'>
		a{
			color: blue;
			text-decoration: none;
			font-size: 14px;
		}
		li.button.last{
			border-left: 1px solid #596A72;
			float: right;
		}
		.S_Blue{
			margin: 0 !important;
		}
	</style>
</head>
<body>
	<ul id="menu_wrap" class="S_Blue">
		<li class="button <?php echo ($page=='home')?'activo':''?>"><a href="<?php echo site_url('clima')?>">DATOS DE CLIMA</a></li>
		<li class="button <?php echo ($page=='estaciones')?'activo':''?>"><a href="<?php echo site_url('clima/estaciones')?>">Estaciones</a></li>
		<li class="button <?php echo ($page=='muestras')?'activo':''?>"><a href="<?php echo site_url('clima/muestras')?>">Muestras</a></li>
		<li class="button <?php echo ($page=='resumenes')?'activo':''?>"><a href="<?php echo site_url('clima/resumenes_diarios')?>">Resumenes</a></li>
		<li class="button <?php echo ($page=='capturas')?'activo':''?>"><a href="<?php echo site_url('clima/capturas')?>">Capturas</a></li>
		<li class="button <?php echo ($page=='pronosticos')?'activo':''?>"><a href="<?php echo site_url('clima/pronosticos')?>">Pronósticos </a></li>
		<li class="button <?php echo ($page=='usuarios')?'activo':''?>"><a href="<?php echo site_url('clima/usuarios')?>">Usuarios </a></li>
		<li class="button <?php echo ($page=='logout')?'activo':''?> last"><a href="<?php echo site_url('clima/logout')?>">Salir</a></li>
	</ul>
	<div class="grosery-crud-container">
		<?php echo $gc->output; ?>
	</div>
	<section class="about">
		<p>Desarrollado por <a href="mailto:victoradrianjimenez@gmail.com" style="font-size:inherit;">Adrian Jimenez</a> - Enero de 2015</p>
	</section>

</body>
</html>
