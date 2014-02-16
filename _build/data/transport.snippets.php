<?php

$snippets = array();

$tmp = array(
	'cslogin_main' => array(
		'file' => 'cslogin_main'
		,'description' => ''
	)
	,'cslogin_userinfo' => array(
		'file' => 'cslogin_userinfo'
		,'description' => ''
	)
);

foreach ($tmp as $k => $v) {
	/* @var modSnippet $snippet */
	$snippet = $modx->newObject('modSnippet');
	$snippet->fromArray(array(
		'id' => 0
		,'name' => $k
		,'description' => @$v['description']
		,'snippet' => getSnippetContent($sources['source_core'].'/elements/snippets/snippet.'.$v['file'].'.php')
		,'static' => BUILD_SNIPPET_STATIC
		,'source' => 1
		,'static_file' => 'core/components/'.PKG_NAME_LOWER.'/elements/snippets/snippet.'.$v['file'].'.php'
	),'',true,true);

	$properties = include $sources['build'].'properties/properties.'.$v['file'].'.php';
	$snippet->setProperties($properties);

	$snippets[] = $snippet;
}

unset($tmp, $properties);
return $snippets;