<?php
include '../config.php';
checkAdmin();

// Get pending transactions
$pending_transactions = $conn->query("SELECT t.*, u.username FROM transactions t 
                                      JOIN users u ON t.user_id = u.id 
                                      WHERE t.status = 'pending' ORDER BY t.created_at DESC");

// Get all users
$users = $conn->query("SELECT id, username, email, balance, created_at FROM users ORDER BY created_at DESC LIMIT 10");

// Get total stats
total_users = $conn->query("SELECT COUNT(*) as count FROM users")->fetch_assoc()['count'];
total_balance = $conn->query("SELECT SUM(balance) as total FROM users")->fetch_assoc()['total'];
total_sold = $conn->query("SELECT COUNT(*) as count FROM game_codes WHERE is_sold = 1")->fetch_assoc()['count'];
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Paneli - Nexus</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body class="nexus-bg">
    <!-- Navigation -->
    <nav class="navbar navbar-dark bg-dark sticky-top">
        <div class="container-fluid">
            <span class="navbar-brand fw-bold">⚡ NEXUS - ADMIN</span>
            <div class="d-flex gap-2">
                <a href="transactions.php" class="btn btn-warning btn-sm">İşlemler</a>
                <a href="users.php" class="btn btn-warning btn-sm">Kullanıcılar</a>
                <a href="codes.php" class="btn btn-warning btn-sm">Kodlar</a>
                <a href="../logout.php" class="btn btn-danger btn-sm">Çıkış</a>
            </div>
        </div>
    </nav>

    <div class="container my-5">
        <h2 class="text-warning mb-4">Admin Paneli</h2>

        <!-- Stats -->
        <div class="row g-4 mb-5">
            <div class="col-md-3">
                <div class="card bg-dark border-warning text-center">
                    <div class="card-body">
                        <h6 class="text-muted">Toplam Kullanıcı</h6>
                        <h2 class="text-warning"><?php echo $total_users; ?></h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-dark border-warning text-center">
                    <div class="card-body">
                        <h6 class="text-muted">Satılan Kodlar</h6>
                        <h2 class="text-warning"><?php echo $total_sold; ?></h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-dark border-warning text-center">
                    <div class="card-body">
                        <h6 class="text-muted">Toplam Bakiye</h6>
                        <h2 class="text-warning"><?php echo number_format($total_balance, 2); ?> TL</h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-dark border-warning text-center">
                    <div class="card-body">
                        <h6 class="text-muted">Beklenen İşlemler</h6>
                        <h2 class="text-warning"><?php echo $pending_transactions->num_rows; ?></h2>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pending Transactions -->
        <div class="card bg-dark border-warning mb-5">
            <div class="card-body">
                <h5 class="card-title text-warning">Beklemede Olan Bakiye Yüklemeleri</h5>
                <div class="table-responsive">
                    <table class="table table-dark table-striped">
                        <thead>
                            <tr class="text-warning">
                                <th>Kullanıcı</th>
                                <th>Tutar</th>
                                <th>IBAN</th>
                                <th>Tarih</th>
                                <th>İşlem</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($trans = $pending_transactions->fetch_assoc()): ?>
                            <tr class="text-light">
                                <td><?php echo $trans['username']; ?></td>
                                <td><?php echo number_format($trans['amount'], 2); ?> TL</td>
                                <td><?php echo substr_replace($trans['iban'], '****', 5, 15); ?></td>
                                <td><?php echo date('d.m.Y H:i', strtotime($trans['created_at'])); ?></td>
                                <td>
                                    <a href="approve_transaction.php?id=<?php echo $trans['id']; } ?>" 
                                       class="btn btn-success btn-sm">Onayla</a>
                                    <a href="reject_transaction.php?id=<?php echo $trans['id']; ?>" 
                                       class="btn btn-danger btn-sm">Reddet</a>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
