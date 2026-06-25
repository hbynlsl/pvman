<?php
return [
	"default" => "sqlite",
	"connections" => [
		"mysql" => [
			"driver" => "mysql",
			"host" => "127.0.0.1",
			"port" => "3306",
			"database" => "your_database",
			"username" => "your_username",
			"password" => "your_password",
			"charset" => "utf8mb4",
			"collation" => "utf8mb4_general_ci",
			"prefix" => "",
			"strict" => true,
			"engine" => null,
			"options" => [
				PDO::ATTR_EMULATE_PREPARES => false, // Must be false for Swoole and Swow drivers.
			],
			"pool" => [
				"max_connections" => 5,
				"min_connections" => 1,
				"wait_timeout" => 3,
				"idle_timeout" => 60,
				"heartbeat_interval" => 50,
			],
		],
		"sqlite" => [
			"driver" => "sqlite",
			"database" => base_path("test.db"),
			"prefix" => "",
			"pool" => [
				// 连接池配置，仅支持swoole/swow驱动
				"max_connections" => 5, // 最大连接数
				"min_connections" => 1, // 最小连接数
				"wait_timeout" => 3, // 从连接池获取连接等待的最大时间，超时后会抛出异常
				"idle_timeout" => 60, // 连接池中连接最大空闲时间，超时后会关闭回收，直到连接数为min_connections
				"heartbeat_interval" => 50, // 连接池心跳检测时间，单位秒，建议小于60秒
			],
		],
		"pgsql" => [
			"driver" => "pgsql",
			"host" => "127.0.0.1",
			"port" => "5432",
			"database" => "your_database",
			"username" => "your_username",
			"password" => "your_password",
			"charset" => "utf8",
			"prefix" => "",
			"schema" => "public",
			"sslmode" => "prefer",
			"pool" => [
				"max_connections" => 5,
				"min_connections" => 1,
				"wait_timeout" => 3,
				"idle_timeout" => 60,
				"heartbeat_interval" => 50,
			],
		],
	],
];
