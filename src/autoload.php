<?php
require_once(__DIR__ . "/SplClassLoader.php");
$splClassLoader = new SplClassLoader("Peach", __DIR__);
$splClassLoader->register();
