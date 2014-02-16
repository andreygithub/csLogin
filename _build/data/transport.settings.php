<?php

$settings = array();

$tmp = array(
	'mail_from' => array(
            'value' => 'andreyzagorets@gmail.com'
            ,'xtype' => 'textfield'
            ,'area' => ''
	)
	,'mail_from_name' => array(
            'value' => 'euroelectric.kz'
            ,'xtype' => 'textfield'
            ,'area' => ''
	)

	,'mail_subject_registration' => array(
            'value' => 'Подтверждение регистрации'	
            ,'xtype' => 'textarea'
            ,'area' => ''
	)
);

foreach ($tmp as $k => $v) {
	/* @var modSystemSetting $setting */
	$setting = $modx->newObject('modSystemSetting');
	$setting->fromArray(array_merge(
		array(
			'key' => 'cslogin.'.$k
			,'namespace' => 'cslogin'
		), $v
	),'',true,true);

	$settings[] = $setting;
}

unset($tmp);
return $settings;