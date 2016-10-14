<?php
return array(
	'_root_'  => 'top/index',  // The default route

    'admin' => 'admin/index',
    'admin/signin' => 'admin/signin',
    'admin/list' => 'admin/list',
    'admin/detail/(:num)' => 'admin/detail/$1',

	'_404_'   => 'top/404',    // The main 404 route
);
