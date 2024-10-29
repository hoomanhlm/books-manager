<?php
/**
 * Configuration settings for the Books Manager plugin.
 *
 * This array defines essential paths and settings used by the plugin.
 *
 * - 'views_path': Directory path for view templates.
 * - 'logs_path': Directory path where log files are stored.
 * - 'logs_days': Number of days to retain log files before deletion.
 *
 * @var array $config
 * @since 1.0
 */

$config = array(
	'views_path' => 'views',
	'logs_path'  => 'storage/logs',
	'logs_days'  => 30,
);
