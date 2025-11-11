<?php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['user']) || ($_SESSION['user']['role'] !== 'admin' && $_SESSION['user']['role'] !== 'cashier')) {
    header('Location: ../cashier-dashboard.php?error=Insufficient permissions');
    exit();
}

$database = new Database();
$db = $database->getConnection();

if ($_POST['action'] === 'complete_payment') {
    $queueId = $_POST['queue_id'];
    $amount = $_POST['amount'];
    $paymentType = $_POST['payment_type'];
    $cashierId = $_SESSION['user']['id'];
    
    try {
        $db->beginTransaction();
        
        // Insert transaction
        $transactionQuery = "
            INSERT INTO transactions (queue_id, amount, payment_type, cashier_id, date_paid) 
            VALUES (:queue_id, :amount, :payment_type, :cashier_id, NOW())
        ";
        $transactionStmt = $db->prepare($transactionQuery);
        $transactionStmt->bindParam(':queue_id', $queueId);
        $transactionStmt->bindParam(':amount', $amount);
        $transactionStmt->bindParam(':payment_type', $paymentType);
        $transactionStmt->bindParam(':cashier_id', $cashierId);
        $transactionStmt->execute();
        $transactionId = $db->lastInsertId();
        
        // Update queue status
        $queueQuery = "UPDATE queue SET status = 'served', time_out = NOW() WHERE queue_id = :queue_id";
        $queueStmt = $db->prepare($queueQuery);
        $queueStmt->bindParam(':queue_id', $queueId);
        $queueStmt->execute();
        
        // Get student ID
        $studentQuery = "SELECT student_id FROM queue WHERE queue_id = :queue_id";
        $studentStmt = $db->prepare($studentQuery);
        $studentStmt->bindParam(':queue_id', $queueId);
        $studentStmt->execute();
        $queueData = $studentStmt->fetch(PDO::FETCH_ASSOC);
        
        // Add to payment history
        $historyQuery = "
            INSERT INTO payment_history (student_id, transaction_id, status, date) 
            VALUES (:student_id, :transaction_id, 'completed', NOW())
        ";
        $historyStmt = $db->prepare($historyQuery);
        $historyStmt->bindParam(':student_id', $queueData['student_id']);
        $historyStmt->bindParam(':transaction_id', $transactionId);
        $historyStmt->execute();
        
        $db->commit();
        header('Location: ../cashier-dashboard.php?message=Payment completed successfully');
        
    } catch (Exception $e) {
        $db->rollBack();
        header('Location: ../cashier-dashboard.php?error=Payment failed: ' . $e->getMessage());
    }
    exit();
}
?>