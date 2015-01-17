<!DOCTYPE html>
<!--[if lt IE 7]> <html class="lt-ie9 lt-ie8 lt-ie7" lang="es"> <![endif]-->
<!--[if IE 7]> <html class="lt-ie9 lt-ie8" lang="es"> <![endif]-->
<!--[if IE 8]> <html class="lt-ie9" lang="es"> <![endif]-->
<!--[if gt IE 8]><!--> <html lang="es"> <!--<![endif]-->
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
  <title>Datos de clima - Cambiar clave</title>
  <link rel="shortcut icon" href="<?php echo site_url('images/favicon.ico')?>" />
  <link rel="stylesheet" href="<?php echo site_url('css/login.css')?>">
  <link rel="stylesheet" href="<?php echo site_url('css/style.css')?>">
</head>
<body>
  <section class="container-login">
    <div class="login">
      <h1>Cambiar clave</h1>
      <form method="post" action="<?php echo site_url('clima/cambiar_clave').'/'.$code?>">
	<input type="hidden" name="<?php echo array_keys($csrf)[0]?>" value="<?php echo array_values($csrf)[0]?>">
	<input type="hidden" name="user_id" value="<?php echo $user_id?>">

        <p><input type="password" name="pass" value="" required="required" placeholder="Nueva contraseña"></p>
        <p><input type="password" name="pass2" value="" required="required" placeholder="Repetir contraseña"></p>

	<div class="captcha"></div>
        <p class="submit"><input type="submit" name="commit" value="Enviar"></p>
      </form>
      <?php if (strlen($message)>0): ?>
        <div class="login-msg"><?php echo $message?></div>
      <?php endif?>
    </div>
  </section>

  <section class="about">
    <p>Desarrollado por <a href="mailto:victoradrianjimenez@gmail.com" style="font-size:inherit;">Adrian Jimenez</a> - Enero de 2015</p>
  </section>
</body>
</html>

