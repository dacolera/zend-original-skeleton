<?php
$BASE_HREF = 'http://localhost:8888/skeleton';

return array(
	'project' => 'Skeleton',
	'projectTitle' => "Skeleton",
	'paths' => array(
		'admin' => array(
			'root' => APPLICATION_ROOT .'/modules/admin/views',
			'file' => APPLICATION .'/public/admin',
			'base' => $BASE_HREF. '/public/admin'
		),
		'site' => array(
			'root' => APPLICATION_ROOT .'/modules/site/views',
			'file' => APPLICATION .'/public/site',
			'base' => $BASE_HREF. '/public/site'
		)
	)
);