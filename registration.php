<?php
require_once "config.php";

if ($_SERVER["REQUEST_METHOD"] == "POST")  {

    
    $teamname = mysqli_real_escape_string($conn, $_POST['teamname'] ?? '');
    $userid = mysqli_real_escape_string($conn, $_POST['userid'] ?? '');
    $email = mysqli_real_escape_string($conn, $_POST['email'] ?? '');
    $phone = mysqli_real_escape_string($conn, $_POST['phone'] ?? '');
    $team = intval($_POST['team'] ?? 0);

    
    $imagePath = "";
    if (isset($_FILES['team_image']) && $_FILES['team_image']['error'] == 0) {
        $targetDir = "uploads/leaders/";
        if (!file_exists($targetDir)) {
            mkdir($targetDir, 0777, true);
        }

        $fileName = basename($_FILES["team_image"]["name"]);
        $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        $allowedTypes = ["jpg", "jpeg", "png"];
        $newFileName = uniqid("leader_") . "." . $fileExt;
        $targetFile = $targetDir . $newFileName;

        if (in_array($fileExt, $allowedTypes) && $_FILES["team_image"]["size"] <= 2 * 1024 * 1024) {
            if (move_uploaded_file($_FILES["team_image"]["tmp_name"], $targetFile)) {
                $imagePath = $targetFile;
            }
        }
    }

    if ($teamname && $userid && $email && $phone && $team > 0) {
        
        $sql = "INSERT INTO user_registration (teamname, userid, email, phone, team, image)
                VALUES ('$teamname', '$userid', '$email', '$phone', $team, '$imagePath')";
        if ($conn->query($sql)) {
            $user_registred_id = $conn->insert_id;

          
            if (isset($_POST['studentname']) && isset($_POST['studentuserid'])) {
                $studentname = $_POST['studentname'];
                $studentuserid = $_POST['studentuserid']; 
                $memberImages = $_FILES['studentimage'] ?? null;

                for ($i = 0; $i < count($studentname); $i++) {
                    $studentName = mysqli_real_escape_string($conn, trim($studentname[$i]));
                    $studentUserId = mysqli_real_escape_string($conn, trim($studentuserid[$i]));

                    
                    $memberImagePath = "";
                    if ($memberImages && isset($memberImages['name'][$i]) && $memberImages['error'][$i] == 0) {
                        $targetDir = "uploads/members/";
                        if (!file_exists($targetDir)) {
                            mkdir($targetDir, 0777, true);
                        }

                        $fileName = basename($memberImages["name"][$i]);
                        $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
                        $allowedTypes = ["jpg", "jpeg", "png"];
                        $newFileName = uniqid("member_") . "." . $fileExt;
                        $targetFile = $targetDir . $newFileName;

                        if (in_array($fileExt, $allowedTypes) && $memberImages["size"][$i] <= 2 * 1024 * 1024) {
                            if (move_uploaded_file($memberImages["tmp_name"][$i], $targetFile)) {
                                $memberImagePath = $targetFile;
                            }
                        }
                    }

                    $insertMemberSQL = "INSERT INTO team_members_registration 
                        (team, studentname, studentuserid, image, user_registred_id)
                        VALUES ($team, '$studentName', '$studentUserId', '$memberImagePath', $user_registred_id)";
                    $conn->query($insertMemberSQL);
                }
            }

            echo "<script>alert('Registration successful!'); window.location.href='registration.php';</script>";
            exit();
        } else {
            echo "Error: " . $conn->error;
        }
    } else {
        echo "Please fill all required fields.";
    }

    $conn->close();
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Modern User Registration</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background: linear-gradient(to right, #6a11cb, #2575fc);
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
    }
    .card {
      border: none;
      border-radius: 1rem;
    }
    .form-control:focus {
      box-shadow: none;
      border-color: #6a11cb;
    }
  </style>
</head>
<body>
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-md-7 col-lg-6">
        <div class="card shadow-lg">
          <div class="card-body p-4">
            <h3 class="text-center mb-4">Create Account</h3>
            
            
            <form action="" method="POST" enctype="multipart/form-data">
              
              <div class="mb-3">
                <label for="teamname" class="form-label">Team Name</label>
                <input type="text" class="form-control" id="teamname" name="teamname" placeholder="ENTER YOUR TEAM NAME" required>
              </div>
                  
              <div class="mb-3">
                <label for="userid" class="form-label">Leader User ID</label>
                <input type="text" class="form-control" id="userid" name="userid" placeholder="24BCE****" required>
              </div>

              <div class="mb-3">
                <label for="email" class="form-label">Email address</label>
                <input type="email" class="form-control" id="email" name="email" placeholder="ENTER YOUR EMAIL" required>
              </div>

              <div class="mb-3">
                <label for="phone" class="form-label">Phone Number</label>
                <input type="tel" class="form-control" id="phone" name="phone" placeholder="ENTER YOUR PHONE NUMBER" required>
              </div>

          
              <div class="mb-3">
                <label for="team_image" class="form-label">Upload Leader Image</label>
                <input type="file" class="form-control" id="team_image" name="team_image" accept="image/png, image/jpeg" required>
                <small class="text-muted">Allowed: JPG, PNG (Max 2MB)</small>
              </div>

              <div class="mb-3">
                <label for="team" class="form-label">Select Team Size</label>
                <select class="form-select" id="team" name="team" required>
                  <option selected disabled value="">Select Team Size</option>
                  <option value="1">1</option>
                  <option value="2">2</option>
                  <option value="3">3</option>
                  <option value="4">4</option>
                </select>
              </div>
              
              <div class="container mt-4">
                <h5 class="mt-4">Team Members</h5>
                <div id="sections-container"></div>
                <button type="button" class="btn btn-primary mt-2" id="add-members"> Add Members</button>
              </div>
              
              <br>
              <div class="d-grid">
                <button type="submit" class="btn btn-primary">Register</button>
              </div>

            </form>
          </div>
        </div>
      </div>
    </div>
  </div>


<script>
document.addEventListener("DOMContentLoaded", function () {
  const dropdown = document.getElementById("team");
  const sectionsContainer = document.getElementById("sections-container");
  const addSectionBtn = document.getElementById("add-members");

  let selectedCount = 0;

  function createSectionRow() {
    const row = document.createElement("div");
    row.classList.add("row", "mb-3", "section-row");

    row.innerHTML = `
      <div class="col-md-3">
        <input type="text" class="form-control" name="studentname[]" placeholder="Student Name" required>
      </div>
      <div class="col-md-3">
        <input type="text" class="form-control" name="studentuserid[]" placeholder="Student User ID" required>
      </div>
      <div class="col-md-4">
        <input type="file" class="form-control" name="studentimage[]" accept="image/png, image/jpeg" required>
      </div>
      <div class="col-md-2">
        <button type="button" class="btn btn-danger w-100 remove-section">X</button>
      </div>
    `;

    row.querySelector(".remove-section").addEventListener("click", () => {
      row.remove();
      updateAddButtonState();
    });

    return row;
  }

  function updateAddButtonState() {
    const currentCount = sectionsContainer.querySelectorAll(".section-row").length;
    addSectionBtn.disabled = currentCount >= selectedCount;
  }

  dropdown.addEventListener("change", function () {
    selectedCount = parseInt(this.value);
    sectionsContainer.innerHTML = "";

    for (let i = 0; i < selectedCount; i++) {
      sectionsContainer.appendChild(createSectionRow());
    }

    updateAddButtonState();
  });

  addSectionBtn.addEventListener("click", function () {
    const currentCount = sectionsContainer.querySelectorAll(".section-row").length;
    if (currentCount < selectedCount) {
      sectionsContainer.appendChild(createSectionRow());
      updateAddButtonState();
    }
  });
});
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
