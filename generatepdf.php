<?php
require_once "config.php";
require('fpdf/fpdf.php');


if (!isset($_GET['team_id'])) {
    die("Team ID is required.");
}

$team_id = intval($_GET['team_id']);

// Fetch team data
$sql = "SELECT 
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
        WHERE u.user_registred_id = $team_id";

$result = $conn->query($sql);

if ($result->num_rows === 0) {
    die("No data found for the given Team ID.");
}

// Group team data
$team = null;
$members = [];

while ($row = $result->fetch_assoc()) {
    if (!$team) {
        $team = [
            'teamname' => $row['teamname'],
            'leader_userid' => $row['leader_userid'],
            'email' => $row['email'],
            'phone' => $row['phone'],
            'team_size' => $row['team']
        ];
    }
    if ($row['studentname'] || $row['studentuserid']) {
        $members[] = [
            'studentname' => $row['studentname'],
            'studentuserid' => $row['studentuserid']
        ];
    }
}

$conn->close();

// Create PDF
$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial','B',16);
$pdf->Cell(0,10,'Team Details',0,1,'C');

$pdf->SetFont('Arial','',12);
$pdf->Ln(5);
$pdf->Cell(50,10,"Team Name:",0,0);
$pdf->Cell(0,10,$team['teamname'],0,1);
$pdf->Cell(50,10,"Leader User ID:",0,0);
$pdf->Cell(0,10,$team['leader_userid'],0,1);
$pdf->Cell(50,10,"Email:",0,0);
$pdf->Cell(0,10,$team['email'],0,1);
$pdf->Cell(50,10,"Phone:",0,0);
$pdf->Cell(0,10,$team['phone'],0,1);
$pdf->Cell(50,10,"Team Size:",0,0);
$pdf->Cell(0,10,$team['team_size'],0,1);

$pdf->Ln(10);
$pdf->SetFont('Arial','B',14);
$pdf->Cell(0,10,'Team Members',0,1);

$pdf->SetFont('Arial','',12);
if (!empty($members)) {
    foreach ($members as $index => $member) {
        $pdf->Cell(10,10,($index+1).'.',0,0);
        $pdf->Cell(80,10,$member['studentname'] ?: 'Unknown Name',0,0);
        $pdf->Cell(50,10,$member['studentuserid'] ?: 'Unknown ID',0,1);
    }
} else {
    $pdf->Cell(0,10,'No members registered.',0,1);
}

// Output PDF to browser
$pdf->Output("D", "Team_{$team_id}_Details.pdf");
?>
