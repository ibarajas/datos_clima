<html>
<body>
	<h1 style="font-size: 11pt;"><?php echo sprintf(lang('email_forgot_password_heading'), $identity);?></h1>
	<p><?php echo sprintf(lang('email_forgot_password_subheading'), anchor('clima/cambiar_clave/'. $forgotten_password_code, lang('email_forgot_password_link')));?></p>
</body>
</html>
