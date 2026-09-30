<?php
session_start();
$teamSizes = $_SESSION['teamSizes'] ?? [];
$totalStudents = array_sum($teamSizes);

$sizeCounts = [];
foreach ($teamSizes as $size) {
    $sizeCounts[$size] = ($sizeCounts[$size] ?? 0) + 1;
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Team Statistics</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="container py-4">

<h2 class="text-center mb-4">📊 Team Statistics</h2>

<?php if (!empty($teamSizes)): ?>
    <div class="text-center mb-4">
        <h5 class="text-success">👨‍🎓 Total Students: <strong><?= $totalStudents ?></strong></h5>
    </div>

    <!-- Charts Row -->
    <div class="row">
        <div class="col-md-6 mb-4">
            <div class="card shadow">
                <div class="card-header text-center fw-bold">Teams by Size (Pie Chart)</div>
                <div class="card-body">
                    <canvas id="pieChart"></canvas>
                </div>
            </div>
        </div>

        <div class="col-md-6 mb-4">
            <div class="card shadow">
                <div class="card-header text-center fw-bold">Teams by Size (Bar Chart)</div>
                <div class="card-body">
                    <canvas id="barChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Line Graph Row -->
    <div class="row">
        <div class="col-md-12 mb-4">
            <div class="card shadow">
                <div class="card-header text-center fw-bold">Teams by Size (Line Chart)</div>
                <div class="card-body">
                    <canvas id="lineChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Summary Table -->
    <div class="card shadow mt-4">
        <div class="card-header text-center fw-bold">📋 Team Summary</div>
        <div class="card-body">
            <table class="table table-bordered text-center">
                <thead class="table-primary">
                    <tr>
                        <th>Team Size</th>
                        <th>Number of Teams</th>
                        <th>Total Students</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($sizeCounts as $size => $count): ?>
                        <tr>
                            <td><?= $size ?></td>
                            <td><?= $count ?></td>
                            <td><?= $size * $count ?></td>
                        </tr>
                    <?php endforeach; ?>
                    <tr class="table-success fw-bold">
                        <td colspan="2">Overall Total</td>
                        <td><?= $totalStudents ?></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

<?php else: ?>
    <div class="alert alert-warning text-center">⚠️ No team data available.</div>
<?php endif; ?>

<script>
    const sizeCounts = <?= json_encode($sizeCounts) ?>;
    const labels = Object.keys(sizeCounts);
    const data = Object.values(sizeCounts);

    // Pie Chart
    new Chart(document.getElementById("pieChart"), {
        type: "radar",
        data: {
            labels: labels.map(s => "Size " + s),
            datasets: [{ 
                data: data,
                backgroundColor: ["#4e79a7", "#f28e2b", "#76b7b2", "#e15759", "#59a14f"]
            }]
        }
    });

    // Bar Chart
    new Chart(document.getElementById("barChart"), {
        type: "bar",
        data: {
            labels: labels.map(s => "Size " + s),
            datasets: [{ 
                label: "Teams", 
                data: data, 
                backgroundColor: "#4e79a7" 
            }]
        },
        options: { scales: { y: { beginAtZero: true } } }
    });


    new Chart(document.getElementById("lineChart"), {
        type: "line",
        data: {
            labels: labels.map(s => "Size " + s),
            datasets: [{ 
                label: "Teams", 
                data: data, 
                borderColor: "#e15759", 
                backgroundColor: "rgba(225,87,89,0.3)",
                fill: true,
                tension: 0.3, 
                pointBackgroundColor: "#e15759",
                pointRadius: 5
            }]
        },
        options: {
            responsive: true,
            scales: { y: { beginAtZero: true } }
        }
    });
</script>

</body>
</html>
