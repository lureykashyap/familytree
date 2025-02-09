<?php
require __DIR__ . '/vendor/autoload.php';

use Kreait\Firebase\Factory;

$factory = (new Factory)
    ->withServiceAccount('path/to/your/firebase_credentials.json')
    ->withDatabaseUri('https://adhikari-tree.firebaseio.com');

$firestore = $factory->createFirestore();
$storage = $factory->createStorage();

$db = $firestore->database();
?>

