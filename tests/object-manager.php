<?php
require dirname(__DIR__).'/vendor/autoload.php';
$kernel = new App\Kernel('dev', true);
$kernel->boot();
return $kernel->getContainer()->get('doctrine')->getManager();
