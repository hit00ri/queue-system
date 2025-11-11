<?php
session_start();
// Simple session check - no auto-redirect
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SLC College Queuing System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
</head>
<body class="student-view">
    <div class="container-fluid vh-100 d-flex align-items-center justify-content-center">
        <div class="row w-100">
            <div class="col-md-6 mx-auto">
                <div class="card shadow-lg">
                    <div class="card-header bg-primary text-white text-center">
                        <h3><i class="fas fa-university me-2"></i>SLC College Queuing System</h3>
                    </div>
                    <div class="card-body p-4">
                        <!-- Display messages -->
                        <?php if (isset($_GET['message'])): ?>
                            <div class="alert alert-info"><?php echo htmlspecialchars($_GET['message']); ?></div>
                        <?php endif; ?>
                        
                        <?php if (isset($_GET['error'])): ?>
                            <div class="alert alert-danger"><?php echo htmlspecialchars($_GET['error']); ?></div>
                        <?php endif; ?>

                        <ul class="nav nav-tabs" id="loginTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="student-tab" data-bs-toggle="tab" data-bs-target="#student" type="button" role="tab">Student</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="staff-tab" data-bs-toggle="tab" data-bs-target="#staff" type="button" role="tab">Staff Login</button>
                            </li>
                        </ul>
                        
                        <div class="tab-content mt-3" id="loginTabsContent">
                            <!-- Student Login -->
                            <div class="tab-pane fade show active" id="student" role="tabpanel">
                                <form method="POST" action="api/auth.php">
                                    <input type="hidden" name="action" value="student_login">
                                    <div class="mb-3">
                                        <label for="studentId" class="form-label">Student ID</label>
                                        <input type="text" class="form-control" id="studentId" name="studentId" placeholder="Enter your student ID" required>
                                    </div>
                                    <button type="submit" class="btn btn-primary w-100">Enter Queue System</button>
                                    <div class="mt-3 text-center">
                                        <small class="text-muted">Use Student ID: 1, 2, or 3</small>
                                    </div>
                                </form>
                            </div>
                            
                            <!-- Staff Login -->
                            <div class="tab-pane fade" id="staff" role="tabpanel">
                                <form method="POST" action="api/auth.php">
                                    <input type="hidden" name="action" value="staff_login">
                                    <div class="mb-3">
                                        <label for="username" class="form-label">Username</label>
                                        <input type="text" class="form-control" id="username" name="username" placeholder="Enter username" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="password" class="form-label">Password</label>
                                        <input type="password" class="form-control" id="password" name="password" placeholder="Enter password" required>
                                    </div>
                                    <button type="submit" class="btn btn-primary w-100">Login</button>
                                    <div class="mt-2 text-center">
                                        <small class="text-muted">Admin: admin/123 | Cashier: cashier1/456</small>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>