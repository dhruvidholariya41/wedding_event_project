<?php
session_start();
// ડેશબોર્ડ મુજબ ડાયરેક્ટ ફાઈલનું નામ (બંને એક જ એડમિન ફોલ્ડરમાં છે)
include("db_connect.php");

// ✅ Only Admin Access
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: ../pages/login.php");
    exit();
}

// ૧. કેન્સલ થયેલા બધા બુકિંગ્સ ફેચ કરો
$sql = "SELECT * FROM cancelled_bookings ORDER BY cancelled_at DESC";
$result = mysqli_query($conn, $sql);

// ૨. ઇન્ક્વાયરી મેસેજ કાઉન્ટ માટે (સાઇડબાર માટે)
$msg_res = mysqli_query($conn, "SELECT COUNT(*) as total FROM contact_messages");
$msg_count = ($msg_res) ? mysqli_fetch_assoc($msg_res)['total'] : 0;
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Cancelled Bookings - Admin Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --sidebar-bg: #111827;
            --accent: #ec4899;
            --main-bg: #f8fafc;
        }

        body {
            margin: 0;
            font-family: 'Segoe UI', Arial, sans-serif;
            background: var(--main-bg);
            display: flex;
        }

        /* Sidebar Style */
        .sidebar {
            width: 250px;
            height: 100vh;
            background: var(--sidebar-bg);
            position: fixed;
            color: white;
            transition: 0.3s;
            z-index: 1000;
        }

        .sidebar h2 {
            text-align: center;
            padding: 25px 10px;
            color: var(--accent);
            border-bottom: 1px solid #1f2937;
            margin: 0;
            font-size: 22px;
        }

        .sidebar a {
            display: block;
            color: #9ca3af;
            padding: 14px 25px;
            text-decoration: none;
            font-size: 14px;
            transition: 0.3s;
        }

        .sidebar a i {
            margin-right: 12px;
            width: 18px;
            text-align: center;
        }

        .sidebar a:hover,
        .sidebar a.active {
            background: #1f2937;
            color: white;
            border-left: 4px solid var(--accent);
        }

        /* Main Content Area */
        .main {
            margin-left: 250px;
            width: calc(100% - 250px);
            padding: 40px;
        }

        .header {
            margin-bottom: 35px;
        }

        .header h1 {
            font-size: 26px;
            color: #1e293b;
            margin: 0;
            font-weight: 700;
        }

        .table-container {
            background: white;
            padding: 25px;
            border-radius: 16px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.03);
            border: 1px solid #f1f5f9;
        }

        .status-badge {
            background: #fee2e2;
            color: #b91c1c;
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            border: 1px solid #fecaca;
        }

        .table thead th {
            background-color: #f8fafc;
            color: #64748b;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-top: none;
        }
    </style>
</head>

<body>



    <div class="main">
        <div class="header">
            <h1>Cancelled Bookings ❌</h1>
            <p class="text-muted">History of all events cancelled by customers.</p>

            <a href="dashboard.php">
<button style="
background:#007bff;
color:white;
padding:8px 15px;
border:none;
border-radius:5px;
cursor:pointer;">
⬅ Back to Dashboard
</button>
</a>
        </div>
       
        <div class="table-container">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th class="py-3">Log ID</th>
                            <th class="py-3">Customer</th>
                            <th class="py-3">Event</th>
                            <th class="py-3">Event Date</th>
                            <th class="py-3">Amount</th>
                            <th class="py-3">Cancelled At</th>
                            <th class="py-3">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if ($result && mysqli_num_rows($result) > 0) {
                            while ($row = mysqli_fetch_assoc($result)) {

                                // ✅ ERROR FIX LOGIC
                                // જો 'amount' કી નથી મળતી, તો 'payable_amount' ચેક કરશે, નહીંતર 0.
                                $final_amt = 0;
                                if (isset($row['amount'])) {
                                    $final_amt = $row['amount'];
                                } elseif (isset($row['payable_amount'])) {
                                    $final_amt = $row['payable_amount'];
                                }

                                echo "<tr>
                                <td><span class='text-muted small'>#" . $row['id'] . "</span></td>
                                <td><div class='fw-bold'>" . htmlspecialchars($row['username']) . "</div></td>
                                <td><span class='badge bg-light text-dark border px-3'>" . ucfirst($row['event_type']) . "</span></td>
                                <td>" . date('d M, Y', strtotime($row['event_date'])) . "</td>
                                <td class='fw-bold'>₹" . number_format((float) ($final_amt ?? 0), 2) . "</td>
                                <td class='text-muted small'>" . date('d M Y, h:i A', strtotime($row['cancelled_at'])) . "</td>
                                <td><span class='status-badge'>Cancelled</span></td>
                            </tr>";
                            }
                        } else {
                            echo "<tr><td colspan='7' class='text-center py-5 text-muted'>
                                No cancellation records found.
                              </td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>



        </div>
    </div>

</body>

</html>