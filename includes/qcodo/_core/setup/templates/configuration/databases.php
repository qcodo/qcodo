<?php
	switch (SERVER_INSTANCE) {
		case 'dev':
		case 'prod':
			return array(
				'main' => array(
					'adapter'	=> 'MySqli5',
					'server'	=> '127.0.0.1',
					'port'		=> null,
					'database'	=> 'qcodo',
					'username'	=> 'root',
					'password'	=> '',
					'profiling'	=> false
				)
			);
	}