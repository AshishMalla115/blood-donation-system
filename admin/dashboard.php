<?php
session_start();
require_once '../config/db.php';

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

// Handle donor verification
if (isset($_POST['verify_donor'])) {
    $donor_id = $_POST['donor_id'];
    $stmt = $pdo->prepare("UPDATE users SET verified = TRUE WHERE id = ? AND role = 'donor'");
    $stmt->execute([$donor_id]);
}

// Get unverified donors
$stmt = $pdo->prepare("SELECT * FROM users WHERE role = 'donor' AND verified = FALSE");
$stmt->execute();
$unverified_donors = $stmt->fetchAll();

// Get all donation requests
$stmt = $pdo->prepare("SELECT dr.*, u.name, u.email, u.phone 
                      FROM donation_requests dr 
                      JOIN users u ON dr.requester_id = u.id 
                      ORDER BY dr.date_requested DESC");
$stmt->execute();
$requests = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Blood Donation System</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>Admin Dashboard</h2>
            <a href="../logout.php" class="btn btn-danger">Logout</a>
        </div>

        <div class="section">
            <h3>Unverified Donors</h3>
            <?php if (count($unverified_donors) > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Blood Group</th>
                            <th>Location</th>
                            <th>Phone</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($unverified_donors as $donor): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($donor['name']); ?></td>
                                <td><?php echo htmlspecialchars($donor['email']); ?></td>
                                <td><?php echo htmlspecialchars($donor['blood_group']); ?></td>
                                <td><?php echo htmlspecialchars($donor['location']); ?></td>
                                <td><?php echo htmlspecialchars($donor['phone']); ?></td>
                                <td>
                                    <form method="POST" style="display: inline;">
                                        <input type="hidden" name="donor_id" value="<?php echo $donor['id']; ?>">
                                        <button type="submit" name="verify_donor" class="btn btn-success">Verify</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>No unverified donors.</p>
            <?php endif; ?>
        </div>

        <div class="section">
            <h3>Donation Requests</h3>
            <?php if (count($requests) > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Requester</th>
                            <th>Blood Group</th>
                            <th>Location</th>
                            <th>Status</th>
                            <th>Date Requested</th>
                            <th>Contact</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($requests as $request): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($request['name']); ?></td>
                                <td><?php echo htmlspecialchars($request['blood_group']); ?></td>
                                <td><?php echo htmlspecialchars($request['location']); ?></td>
                                <td><?php echo htmlspecialchars($request['status']); ?></td>
                                <td><?php echo date('Y-m-d', strtotime($request['date_requested'])); ?></td>
                                <td>
                                    <a href="mailto:<?php echo $request['email']; ?>" class="btn btn-small">Email</a>
                                    <a href="tel:<?php echo $request['phone']; ?>" class="btn btn-small">Call</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>No donation requests.</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html> 