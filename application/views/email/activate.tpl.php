<html>
<body>
	<h1 style="font-size: 11pt;"><?php echo sprintf(lang('email_activate_heading'), $identity);?></h1>
	<p><?php echo sprintf(lang('email_activate_subheading'), anchor('clima/activar/'. $id .'/'. $activation, lang('email_activate_link')));?></p>
</body>
</html>
