<?php
require_once "config.php";
session_start();

$sql = "SELECT 
            u.user_registred_id, 
            u.teamname, 
            u.userid AS leader_userid, 
            u.email, 
            u.phone, 
            u.team, 
            m.studentname, 
            m.studentuserid 
        FROM user_registration u
        LEFT JOIN team_members_registration m 
            ON u.user_registred_id = m.user_registred_id
        ORDER BY u.user_registred_id, m.team_members_id";

$result = $conn->query($sql);

// Group data by team
$teams = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $id = $row['user_registred_id'];
        if (!isset($teams[$id])) {
            $teams[$id] = [
                'teamname' => $row['teamname'],
                'leader_userid' => $row['leader_userid'],
                'email' => $row['email'],
                'phone' => $row['phone'],
                'team_size' => $row['team'],
                'members' => []
            ];
        }
        if ($row['studentname'] || $row['studentuserid']) {
            $teams[$id]['members'][] = [
                'studentname' => $row['studentname'],
                'studentuserid' => $row['studentuserid']
            ];
        }
    }
}

// Collect actual team sizes for charts
$teamSizes = [];
foreach ($teams as $teamId => $team) {
    $count = count($team['members']); // actual registered members
    $teamSizes[] = $count;
}

// Save into session for stats page
$_SESSION['teamSizes'] = $teamSizes;

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Registered Teams and Members</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <style>
        body {
            background: #f8f9fa;
        }
        .team-card {
            transition: box-shadow 0.3s ease;
        }
        .team-card:hover {
            box-shadow: 0 0.5rem 1rem rgb(0 0 0 / 0.15);
        }
        .member-list {
            background: #fff;
            border-top: 1px solid #dee2e6;
        }
        .member-list li {
            padding: 0.5rem 1rem;
            border-bottom: 1px solid #eee;
        }
        .member-list li:last-child {
            border-bottom: none;
        }
        .collapse-btn {
            cursor: pointer;
            user-select: none;
        }
    </style>
</head>
<body>
<div class="container py-5">
    <h1 class="mb-4 text-center text-primary fw-bold">Registered Teams & Members</h1>

    <?php if (!empty($teams)): ?>
        <div class="row g-4">
            <?php foreach ($teams as $teamId => $team): ?>
                <div class="col-md-6 col-lg-4">
                    <div class="card team-card shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title text-primary fw-bold"><?= htmlspecialchars($team['teamname']) ?></h5>
                            <p class="mb-1"><strong>Team ID:</strong> <?= htmlspecialchars($teamId) ?></p>
                            <p class="mb-1"><strong>Leader User ID:</strong> <?= htmlspecialchars($team['leader_userid']) ?></p>
                            <p class="mb-1"><strong>Email:</strong> <a href="mailto:<?= htmlspecialchars($team['email']) ?>"><?= htmlspecialchars($team['email']) ?></a></p>
                            <p class="mb-1"><strong>Phone:</strong> <?= htmlspecialchars($team['phone']) ?></p>
                            <p class="mb-3"><strong>Team Size (Declared):</strong> <?= (int)$team['team_size'] ?></p>

                            <?php if (!empty($team['members'])): ?>
                                <button class="btn btn-sm btn-outline-primary collapse-btn" 
                                        data-bs-toggle="collapse" 
                                        data-bs-target="#members-<?= $teamId ?>" 
                                        aria-expanded="false" 
                                        aria-controls="members-<?= $teamId ?>">
                                    View Team Members (<?= count($team['members']) ?>)
                                </button>
                                <a href="generatepdf.php?team_id=<?= $teamId ?>" target="_blank" class="btn btn-sm btn-outline-success ms-2">
                                    Download PDF
                                </a>
                                <ul class="list-group list-group-flush collapse member-list mt-3" id="members-<?= $teamId ?>">
                                    <?php foreach ($team['members'] as $member): ?>
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            <span><?= htmlspecialchars($member['studentname']) ?: '<em>Unknown Name</em>' ?></span>
                                            <small class="text-muted"><?= htmlspecialchars($member['studentuserid']) ?: '<em>Unknown ID</em>' ?></small>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php else: ?>
                                <p class="text-muted fst-italic">No team members registered.</p>
                                <a href="generate_pdf.php?team_id=<?= $teamId ?>" target="_blank" class="btn btn-sm btn-outline-success">
                                    Download PDF
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Link to charts page -->
        <div class="text-center my-5">
            <a href="team_status.php" class="btn btn-lg btn-success"> View Team Statistics</a>
        </div>
    <?php else: ?>
        <div class="alert alert-warning text-center" role="alert">
            No registrations found.
        </div>
    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
