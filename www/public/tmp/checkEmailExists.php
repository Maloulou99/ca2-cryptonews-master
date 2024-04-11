<?php

use Student\SlimSkeleton\Model\Repository\PDOSingleton;

require_once 'www/model/repository/PDOSingleton.php';

$email = $_POST['email'] ?? '';


$pdoSingleton = PDOSingleton::getInstance('username', 'password', 'localhost', '3307', 'lscryptonews');
$pdo = $pdoSingleton->getConnection();

$stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE email = ?");
$stmt->execute([$email]);
$count = $stmt->fetchColumn();

echo json_encode(['exists' => $count > 0]);
