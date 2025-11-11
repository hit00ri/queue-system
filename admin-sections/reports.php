<?php
// Get report data
$dailyQuery = "
    SELECT 
        DATE(date_paid) as date,
        COUNT(*) as transactions,
        SUM(amount) as revenue
    FROM transactions 
    WHERE date_paid >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
    GROUP BY DATE(date_paid)
    ORDER BY date DESC
";
$dailyStmt = $db->prepare($dailyQuery);
$dailyStmt->execute();
$dailyReports = $dailyStmt->fetchAll(PDO::FETCH_ASSOC);
?>
<div class="card">
    <div class="card-header">
        <h4 class="mb-0"><i class="fas fa-chart-bar me-2"></i>Reports & Analytics</h4>
    </div>
    <div class="card-body">
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card text-center">
                    <div class="card-body">
                        <i class="fas fa-receipt fa-2x text-primary mb-2"></i>
                        <h5>Daily Transactions</h5>
                        <p class="text-muted">Last 7 days summary</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-center">
                    <div class="card-body">
                        <i class="fas fa-users fa-2x text-info mb-2"></i>
                        <h5>Queue Analytics</h5>
                        <p class="text-muted">Queue performance</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-center">
                    <div class="card-body">
                        <i class="fas fa-money-bill-wave fa-2x text-success mb-2"></i>
                        <h5>Revenue Reports</h5>
                        <p class="text-muted">Income analysis</p>
                    </div>
                </div>
            </div>
        </div>

        <h5>Recent Daily Transactions</h5>
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Transactions</th>
                        <th>Revenue</th>
                        <th>Average</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($dailyReports as $report): ?>
                        <tr>
                            <td><?php echo date('M d, Y', strtotime($report['date'])); ?></td>
                            <td><?php echo $report['transactions']; ?></td>
                            <td>₱<?php echo number_format($report['revenue'], 2); ?></td>
                            <td>₱<?php echo number_format($report['revenue'] / max($report['transactions'], 1), 2); ?></td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (empty($dailyReports)): ?>
                        <tr>
                            <td colspan="4" class="text-center text-muted py-3">No transaction data available</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>