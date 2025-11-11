<?php
// Get dashboard statistics
$statsQuery = "
    SELECT 
        (SELECT COUNT(*) FROM queue WHERE status = 'waiting') as waiting,
        (SELECT COUNT(*) FROM queue WHERE status = 'serving') as serving,
        (SELECT COUNT(*) FROM queue WHERE DATE(time_in) = CURDATE()) as today_queued,
        (SELECT COUNT(*) FROM transactions WHERE DATE(date_paid) = CURDATE()) as today_transactions,
        (SELECT SUM(amount) FROM transactions WHERE DATE(date_paid) = CURDATE()) as today_revenue
";
$statsStmt = $db->prepare($statsQuery);
$statsStmt->execute();
$stats = $statsStmt->fetch(PDO::FETCH_ASSOC);
?>
<div class="card">
    <div class="card-header">
        <h4 class="mb-0"><i class="fas fa-tachometer-alt me-2"></i>Dashboard Overview</h4>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-3 mb-3">
                <div class="card text-white bg-primary">
                    <div class="card-body">
                        <h4><?php echo $stats['waiting'] ?? 0; ?></h4>
                        <p class="mb-0">Waiting in Queue</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card text-white bg-info">
                    <div class="card-body">
                        <h4><?php echo $stats['serving'] ?? 0; ?></h4>
                        <p class="mb-0">Currently Serving</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card text-white bg-success">
                    <div class="card-body">
                        <h4><?php echo $stats['today_queued'] ?? 0; ?></h4>
                        <p class="mb-0">Today's Queue</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card text-white bg-warning">
                    <div class="card-body">
                        <h4>₱<?php echo number_format($stats['today_revenue'] ?? 0, 2); ?></h4>
                        <p class="mb-0">Today's Revenue</p>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="mt-4">
            <h5>Quick Actions</h5>
            <div class="d-grid gap-2 d-md-flex">
                <a href="cashier-dashboard.php" class="btn btn-primary me-2">
                    <i class="fas fa-cash-register me-1"></i>Go to Cashier View
                </a>
                <a href="admin-dashboard.php?section=reports" class="btn btn-success me-2">
                    <i class="fas fa-chart-bar me-1"></i>View Reports
                </a>
                <a href="admin-dashboard.php?section=users" class="btn btn-info">
                    <i class="fas fa-users me-1"></i>Manage Users
                </a>
            </div>
        </div>
    </div>
</div>