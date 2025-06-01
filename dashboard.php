<?php
session_start();
require_once 'config/db.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$user_role = $_SESSION['user_role'];

// Handle new donation request
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['request_donation'])) {
    $blood_group = $_POST['blood_group'];
    $location = $_POST['location'];
    
    $stmt = $pdo->prepare("INSERT INTO donation_requests (requester_id, blood_group, location) VALUES (?, ?, ?)");
    $stmt->execute([$user_id, $blood_group, $location]);
}

// Search donors
$search_results = [];
$search_performed = false;

if (isset($_GET['search'])) {
    $search_performed = true;
    $blood_group = $_GET['blood_group'];
    $location = $_GET['location'];
    
    $sql = "SELECT * FROM users WHERE role = 'donor' AND verified = TRUE";
    $params = [];
    
    if ($blood_group) {
        $sql .= " AND blood_group = ?";
        $params[] = $blood_group;
    }
    
    if ($location) {
        $sql .= " AND location LIKE ?";
        $params[] = "%$location%";
    }
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $search_results = $stmt->fetchAll();
}

// Get user's donation requests if they're a recipient
$my_requests = [];
if ($user_role == 'recipient') {
    $stmt = $pdo->prepare("SELECT * FROM donation_requests WHERE requester_id = ? ORDER BY date_requested DESC");
    $stmt->execute([$user_id]);
    $my_requests = $stmt->fetchAll();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Blood Donation System</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?></h2>
            <a href="logout.php" class="btn btn-danger">Logout</a>
        </div>

        <div class="section">
            <h3>Search for Donors</h3>
            <form method="GET" class="search-form">
                <div class="form-group">
                    <label>Blood Group:</label>
                    <select name="blood_group">
                        <option value="">Any Blood Group</option>
                        <option value="A+">A+</option>
                        <option value="A-">A-</option>
                        <option value="B+">B+</option>
                        <option value="B-">B-</option>
                        <option value="AB+">AB+</option>
                        <option value="AB-">AB-</option>
                        <option value="O+">O+</option>
                        <option value="O-">O-</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Location:</label>
                    <input type="text" name="location" placeholder="Enter location">
                </div>

                <button type="submit" name="search" class="btn">Search</button>
            </form>

            <?php if ($search_performed): ?>
                <div class="search-results">
                    <h4>Search Results</h4>
                    <?php if (count($search_results) > 0): ?>
                        <table>
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Blood Group</th>
                                    <th>Location</th>
                                    <th>Contact</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($search_results as $donor): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($donor['name']); ?></td>
                                        <td><?php echo htmlspecialchars($donor['blood_group']); ?></td>
                                        <td><?php echo htmlspecialchars($donor['location']); ?></td>
                                        <td>
                                            <a href="mailto:<?php echo $donor['email']; ?>" class="btn btn-small">Email</a>
                                            <a href="tel:<?php echo $donor['phone']; ?>" class="btn btn-small">Call</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <p>No donors found matching your criteria.</p>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>

        <?php if ($user_role == 'recipient'): ?>
            <div class="section">
                <h3>Request Blood Donation</h3>
                <form method="POST" class="request-form">
                    <div class="form-group">
                        <label>Blood Group Needed:</label>
                        <select name="blood_group" required>
                            <option value="">Select Blood Group</option>
                            <option value="A+">A+</option>
                            <option value="A-">A-</option>
                            <option value="B+">B+</option>
                            <option value="B-">B-</option>
                            <option value="AB+">AB+</option>
                            <option value="AB-">AB-</option>
                            <option value="O+">O+</option>
                            <option value="O-">O-</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Location:</label>
                        <input type="text" name="location" required>
                    </div>

                    <button type="submit" name="request_donation" class="btn">Submit Request</button>
                </form>

                <h3>My Donation Requests</h3>
                <?php if (count($my_requests) > 0): ?>
                    <table>
                        <thead>
                            <tr>
                                <th>Blood Group</th>
                                <th>Location</th>
                                <th>Status</th>
                                <th>Date Requested</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($my_requests as $request): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($request['blood_group']); ?></td>
                                    <td><?php echo htmlspecialchars($request['location']); ?></td>
                                    <td><?php echo htmlspecialchars($request['status']); ?></td>
                                    <td><?php echo date('Y-m-d', strtotime($request['date_requested'])); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p>You haven't made any donation requests yet.</p>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</body>
</html> 