<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Uploads - File Storage System</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #121212;
        }
        .card {
            background-color: #1e1e1e;
        }
        .table {
            --bs-table-bg: transparent;
            --bs-table-color: #fff;
        }
        .table th {
            border-bottom-color: #444;
            color: #adb5bd;
            font-weight: 500;
        }
        .table td {
            border-bottom-color: #333;
            vertical-align: middle;
        }
    </style>
</head>
<?php
// your mission in this file:
// 1. Check session for user authentication if not authenticated redirect to login.php
// 2. If authenticated display the list of uploaded files in a table format with the following columns: File Name, Size, Date Uploaded, Actions (Download/Delete)
// 3. makesure just show data from the logged in user

session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php?error=" . urlencode("Please login first"));
    exit();
}

require_once "db.php";

$userId = $_SESSION['user_id'];

$sql = "SELECT * FROM files WHERE user_id = ?";

$statement = $connection->prepare($sql);
$statement->bind_param("i", $userId);
$statement->execute();
$result = $statement->get_result();

?>

<body class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2 class="fw-semibold text-white mb-0">Your Files</h2>
                    <div>
                        <a href="upload.php" class="btn btn-primary me-2 fw-medium">Upload New</a>
                        <a href="index.php" class="btn btn-outline-secondary text-white">Home</a>
                    </div>
                </div>

                <div class="card shadow-lg border-secondary border-opacity-25">
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th scope="col" class="ps-4 py-3">File Name</th>
                                        <th scope="col" class="py-3">Size</th>
                                        <th scope="col" class="py-3">Date Uploaded</th>
                                        <th scope="col" class="text-end pe-4 py-3">Actions</th>
                                    </tr>
                                </thead>
                                <tbody> 
                                    <!-- pengganti dummy -->
                                    <?php while ($file = $result->fetch_assoc()): ?>

                                        <tr>
                                            <td class="ps-4 py-3 text-white">
                                                <?= htmlspecialchars($file['original_name']) ?>
                                            </td>

                                            <td class="py-3 text-white-50">
                                                    <?= round($file['file_size'] / 1024 / 1024, 2) ?> MB
                                            </td>

                                            <td class="py-3 text-white-50">
                                                <?= htmlspecialchars($file['uploaded_at']) ?>
                                            </td>

                                            <td class="text-end pe-4 py-3">
                                                <a href="#" class="btn btn-sm btn-outline-info me-1">
                                                    Download
                                                </a>

                                                <a href="#" class="btn btn-sm btn-outline-danger">
                                                    Delete
                                                </a>
                                            </td>
                                        </tr>

                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                
                <div class="text-center mt-4">
                    <p class="text-white-50 small"> 
                        Showing <?= $result->num_rows ?> files in your vault.
                    </p>
                </div>
            </div>
        </div>
    </div>
    <!-- Bootstrap 5 Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
