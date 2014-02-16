<?php

$properties = array();

$tmp = array(
	'tpl_login_user_no_active' => array(
		'type' => 'textfield'
		,'value' => 'login_user_no_active'
	)
	,'tpl_login_user_active' => array(
		'type' => 'textfield'
		,'value' => 'login_user_active'
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