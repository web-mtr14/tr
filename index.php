<?php

error_reporting(-1);
ini_set('dosplay_errors', 'On');

use Trivago\Recruiting\Entity as Entity;
use Trivago\Recruiting\Service as Service;

require_once(__DIR__  . '/src/SplClassLoader.php');

$oClassLoader = new \SplClassLoader('Trivago', __DIR__ . '/src');
$oClassLoader->register();

try {
    $partnerService = new Service\UnorderedPartnerService(__DIR__  . '/data');
    $hotelService = new Service\UnorderedHotelService($partnerService);
    echo '<pre>';
    print_r($hotelService->getHotelsForCity('Düsseldorf'));
} catch (\InvalidArgumentException $e) {
    die('[INNER ERROR] ' . $e->getMessage());
} catch (Exception $e) {
    die('[OUTER ERROR] ' . $e->getMessage());
}
