<?php

$chunks = array();

$tmp = array(
	'login_main' => array(
		'file' => 'login_main'
		,'description' => ''
	)
	,'login_registration' => array(
		'file' => 'login_registration'
		,'description' => ''
	)
	,'login_user_active' => array(
		'file' => 'login_user_active'
		,'description' => ''
	)
	,'login_user_no_active' => array(
		'file' => 'login_user_no_active'
		,'description' => ''
	)
);

foreach ($tmp as $k => $v) {
	/* @avr modChunk $chunk */
	$chunk = $modx->newObject('modChunk');
	$chunk->fromArray(array(
		'id' => 0
		,'name' => $k
		,'description' => @$v['description']
		,'snippet' => file_get_contents($sources['source_core'].'/elements/chunks/chunk.'.$v['file'].'.tpl')
		,'static' => BUILD_CHUNK_STATIC
		,'source' => 1
		,'static_file' => 'core/components/'.PKG_NAME_LOWER.'/elements/chunks/chunk.'.$v['file'].'.tpl'
	),'',true,true);

	$chunks[] = $chunk;
}

unset($tmp);
return $chunks;