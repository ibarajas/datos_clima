<!DOCTYPE html>
<!--[if lt IE 7]> <html class="lt-ie9 lt-ie8 lt-ie7" lang="en"> <![endif]-->
<!--[if IE 7]> <html class="lt-ie9 lt-ie8" lang="en"> <![endif]-->
<!--[if IE 8]> <html class="lt-ie9" lang="en"> <![endif]-->
<!--[if gt IE 8]><!--> <html lang="en"> <!--<![endif]-->
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
  <title>Login Form</title>
  <link rel="stylesheet" href="<?php echo site_url('css/login.css')?>">
  <link rel="stylesheet" href="<?php echo site_url('css/style.css')?>">
  <!--[if lt IE 9]><script src="//html5shim.googlecode.com/svn/trunk/html5.js"></script><![endif]-->
</head>
<body>
  <section class="container">
    <div class="login">
      <h1>Datos de clima</h1>
      <form method="post" action="<?php echo site_url('clima/recuperar_clave')?>">
        <p><input type="text" name="email" value="<?php echo set_value('email')?>" placeholder="E-mail para recuperar clave"></p>
        <p><input type="text" name="captcha" value="" placeholder="Código de verificación"></p>
	<div class="captcha" style="margin-bottom: -5px;"><?php echo $captcha['image']?></div>
        <p class="submit"><input type="submit" name="commit" value="Enviar"></p>
      </form>
    </div>
  </section>

  <section class="about">
    <p>Desarrollado por <a href="mailto:victoradrianjimenez@gmail.com" style="font-size:inherit;">Adrian Jimenez</a> - Enero de 2015</p>
  </section>
</body>
</html>

