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
        ORDER BY RAND()   -- ✅ pick random teams
        LIMIT 4";         // ✅ only 4 teams
       
$result = $conn->query($sql);

// Group leaders + members
$teams = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $id = $row['user_registred_id'];
        if (!isset($teams[$id])) {
            $teams[$id] = [
                'leader_userid' => $row['leader_userid'],
                'email' => $row['email'],
                'phone' => $row['phone'],
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
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Company Hierarchy</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />

  <!-- Mermaid.js -->
  <script type="module">
    import mermaid from "https://cdn.jsdelivr.net/npm/mermaid@10/dist/mermaid.esm.min.mjs";
    mermaid.initialize({ startOnLoad: true, theme: "default" });
  </script>

  <style>
    body { background: #f8f9fa; }
    .mermaid { 
      text-align: center; 
      background: #fff; 
      border: 1px solid #dee2e6; 
      border-radius: 0.5rem; 
      padding: 1rem; 
      overflow-x: auto;
    }
  </style>
</head>
<body class="container py-5">
  <h2 class="mb-4 text-center text-primary fw-bold">🏢 Company Hierarchy</h2>

  <?php if (!empty($teams)): ?>
    <div class="mermaid">
      graph TD;
      CEO["👑 Likhith Sai<br><small>CEO</small>"]:::ceo;

      <?php foreach ($teams as $teamId => $team): ?>
        L<?= $teamId ?>["👤 <?= htmlspecialchars($team['leader_userid']) ?><br><small>Employee (Leader)</small>"]:::leader;
        CEO --> L<?= $teamId ?>;

        <?php foreach ($team['members'] as $index => $member): ?>
          M<?= $teamId . '_' . $index ?>["👥 <?= htmlspecialchars($member['studentname']) ?: 'Unknown' ?><br><small>Worker</small>"]:::member;
          L<?= $teamId ?> --> M<?= $teamId . '_' . $index ?>;
        <?php endforeach; ?>
      <?php endforeach; ?>

      classDef ceo fill:#cce5ff,stroke:#004085,stroke-width:2px,font-weight:bold;
      classDef leader fill:#d4edda,stroke:#155724,stroke-width:2px,font-weight:bold;
      classDef member fill:#fff3cd,stroke:#856404,stroke-width:2px;
    </div>
  <?php else: ?>
    <div class="alert alert-warning text-center" role="alert">
      No team registrations found.
    </div>
  <?php endif; ?>

  <div class="text-center mt-4">
    <a href="index.php" class="btn btn-outline-secondary">⬅ Back</a>
  </div>
</body>
</html>
