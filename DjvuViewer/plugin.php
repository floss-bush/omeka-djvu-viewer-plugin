<?php

define('COMMENTING_PLUGIN_DIR', PLUGIN_DIR . '/DjvuViewer');
require_once(COMMENTING_PLUGIN_DIR . '/DjVuViewerPlugin.php');

$DjvuViewerPlugin = new DjvuViewerPlugin();
$DjvuViewerPlugin->setUp();
