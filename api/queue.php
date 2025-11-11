<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['user']) && !isset($_SESSION['student'])) {
    header('Location: ../index.php?error=Not authenticated');
    exit();
}

$database = new Database();
$db = $database->getConnection();

if ($_POST['action'] === 'request_queue') {
    if (!isset($_SESSION['student'])) {
        header('Location: ../student-dashboard.php?error=Student not logged in');
        exit();
    }
    
    $studentId = $_POST['student_id'];
    
    // Check for active queue
    $checkQuery = "SELECT * FROM queue WHERE student_id = :student_id AND status IN ('waiting', 'serving')";
    $checkStmt = $db->prepare($checkQuery);
    $checkStmt->bindParam(':student_id', $studentId);
    $checkStmt->execute();
    
    if ($checkStmt->rowCount() > 0) {
        $existing = $checkStmt->fetch(PDO::FETCH_ASSOC);
        header('Location: ../student-dashboard.php?error=You already have queue number ' . $existing['queue_number']);
        exit();
    }
    
    // Get next queue number
    $maxQuery = "SELECT MAX(queue_number) as max_number FROM queue WHERE DATE(time_in) = CURDATE()";
    $maxStmt = $db->prepare($maxQuery);
    $maxStmt->execute();
    $maxResult = $maxStmt->fetch(PDO::FETCH_ASSOC);
    $nextQueueNumber = ($maxResult['max_number'] ?? 0) + 1;
    
    // Insert new queue
    $insertQuery = "INSERT INTO queue (student_id, queue_number, status, time_in) VALUES (:student_id, :queue_number, 'waiting', NOW())";
    $insertStmt = $db->prepare($insertQuery);
    $insertStmt->bindParam(':student_id', $studentId);
    $insertStmt->bindParam(':queue_number', $nextQueueNumber);
    
    if ($insertStmt->execute()) {
        header('Location: ../student-dashboard.php?message=Queue number ' . $nextQueueNumber . ' assigned successfully!');
    } else {
        header('Location: ../student-dashboard.php?error=Failed to get queue number');
    }
    exit();
    
} elseif ($_POST['action'] === 'void_queue') {
    if (!isset($_SESSION['user']) || ($_SESSION['user']['role'] !== 'admin' && $_SESSION['user']['role'] !== 'cashier')) {
        header('Location: ../cashier-dashboard.php?error=Insufficient permissions');
        exit();
    }
    
    $queueId = $_POST['queue_id'];
    $updateQuery = "UPDATE queue SET status = 'voided', time_out = NOW() WHERE queue_id = :queue_id";
    $updateStmt = $db->prepare($updateQuery);
    $updateStmt->bindParam(':queue_id', $queueId);
    
    if ($updateStmt->execute()) {
        header('Location: ../cashier-dashboard.php?message=Queue entry voided successfully');
    } else {
        header('Location: ../cashier-dashboard.php?error=Failed to void queue entry');
    }
    exit();
}
?>