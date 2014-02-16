<?php

$properties = array();

$tmp = array(
	'tpl_main' => array(
		'type' => 'textfield'
		,'value' => 'login_userinfo_main'
	)
	,'tpl_error' => array(
		'type' => 'textfield'
		,'value' => 'cslogin_error'
	)
        ,'tpl_registration_update_mail' => array(
		'type' => 'textfield'
		,'value' => 'login_registration_update_mail'
	)
);

foreach ($tmp as $k => $v) {
	$properties[] = array_merge(array(
			'name' => $k
			,'desc' => 'cslogin_prop_'.$k
			,'lexicon' => 'cslogin:properties'
		), $v
	);
}

return $properties;