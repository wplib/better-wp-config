<?php

return array(
	'environments' => array(
		'(www\.)?example.com'   => 'live',
		'test.example.com'      => 'test',
		'stage.example.com'     => 'stage',
		'(www\.)?example.local' => 'local',
	),
	'configs' => array(
		'(live|test|dev)' => '/wp-content/config',
		'local'           => '/content/config',
	),
);
