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
            u.image AS leader_image, 
            m.studentname, 
            m.studentuserid,
            m.image AS member_image
        FROM user_registration u
        LEFT JOIN team_members_registration m 
            ON u.user_registred_id = m.user_registred_id
        ORDER BY u.user_registred_id, m.team_members_id
        LIMIT 15";

$result = $conn->query($sql);

$data = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $teamId = $row['user_registred_id'];

        // Leader
        if (!isset($data[$teamId])) {
            $data[$teamId] = [
                'id' => "team_$teamId",
                'parent' => 'ceo',
                'name' => $row['teamname'],
                'position' => 'Leader',
                'phone' => $row['phone'],
                'email' => $row['email'],
                'studentuserid' => $row['leader_userid'],
                'isLeader' => true,
                'image' => $row['leader_image'], // ✅ leader image
                'members' => []
            ];
        }

        // Members
        if ($row['studentname'] || $row['studentuserid']) {
            $data[$teamId]['members'][] = [
                'id' => uniqid("member_"),
                'parent' => "team_$teamId",
                'name' => $row['studentname'] ?: "Unknown",
                'position' => 'Member',
                'studentuserid' => $row['studentuserid'] ?: "Unknown",
                'isLeader' => false,
                'image' => $row['member_image'] // ✅ member image
            ];
        }
    }
}

$chartData = [];

// CEO node
$chartData[] = [
    'id' => 'ceo',
    'parent' => '',
    'name' => 'CEO',
    'position' => 'Chief Executive Officer',
    'isLeader' => true,
    'address' => 'Head Office',
    'image' => 'default.png' // ✅ Add company logo or default CEO image
];

// Push leaders + members
foreach ($data as $team) {
    $chartData[] = $team;
    foreach ($team['members'] as $member) {
        $chartData[] = $member;
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Team Organizational Chart</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <script src="https://code.jscharting.com/latest/jscharting.js"></script>
  <style>
    body { background: #f8f9fa; font-family: Arial, sans-serif; }
    .personDescription {
      background-color: #eeeeee;
      padding: 6px;
      border-radius: 6px;
      margin-top: -4px;
      font-size: 13px;
      text-align: center;
    }
    #chartDiv {
      width: 100%;
      max-width: 1000px;
      height: 650px;
      margin: 20px auto;
      overflow: hidden;
    }
    .iconBox {
      margin-bottom: 6px;
      text-align: center;
    }
    .iconBox img {
      width: 60px;
      height: 60px;
      border-radius: 50%;
      object-fit: cover;
      border: 2px solid #0d6efd;
    }
  </style>
</head>
<body>
  <h2 style="text-align:center; color:#0d6efd;">Registered Teams - Organizational Chart</h2>
  <div id="chartDiv"></div>

  <script>
    var data = <?php echo json_encode($chartData, JSON_PRETTY_PRINT); ?>;

    function makeSeries(data) {
      return [{
        points: data.map(function(item) {
          return {
            id: item.id,
            parent: item.parent || null,
            name: item.name,
            attributes: {
              position: item.position,
              phone: item.phone,
              email: item.email,
              studentuserid: item.studentuserid,
              isLeader: item.isLeader,
              image: item.image
            }
          };
        })
      }];
    }

    JSC.chart('chartDiv', {
      type: 'organizational down',
      defaultPoint: {
        focusGlow: false,
        connectorLine: { width: 1, color: '#e0e0e0' },
        annotation: {
          padding: 3,
          asHTML: true,
          margin: [12, 2],
          label: {
            text: function(point) {
              var attrs = point.options('attributes');
              var imgTag = attrs.image
                ? '<div class="iconBox"><img src="' + attrs.image + '" /></div>'
                : '<div class="iconBox"><img src="default.png" /></div>';

              if (attrs.isLeader) {
                return (
                  imgTag +
                  '<div class="personDescription">' +
                    '<b>' + attrs.position + '</b><br/>' +
                    point.name + '<br/>' +
                    'User ID: ' + (attrs.studentuserid || '') + '<br/>' +
                    'Phone: ' + (attrs.phone || '') + '<br/>' +
                    'Email: ' + (attrs.email || '') +
                  '</div>'
                );
              } else {
                return (
                  imgTag +
                  '<div class="personDescription">' +
                    '<b>' + attrs.position + '</b><br/>' +
                    point.name + '<br/>' +
                    'User ID: ' + (attrs.studentuserid || 'Unknown') +
                  '</div>'
                );
              }
            },
            autoWrap: true
          }
        },
        outline_width: 0,
        color: '#333333'
      },
      series: makeSeries(data)
    });
  </script>
</body>
</html>
