<?php 
require 'config.php';

$Sai = "SELECT * FROM city WHERE status = '1'";
$result = $conn->query($Sai);
?>    
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>City Dropdown</title>
</head>
<body>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<div class="col-md-6">
  <label>City</label> 
  <select class="form-control" id="city" name="city"> 
    <?php
    $count = 0;
    while ($row = $result->fetch_assoc()) {
      $likith = $row['cityname'];
      $likki  = $row['cityid'];
      $count++;

      if($count > 3) break;

      
      $class = ($likith == "Bangalore") ? "highlight" : "";
      ?>
      <option value="<?php echo $likki.':'.$likith?>" class="<?php echo $class?>">
        <?php echo $likki.": ".$likith?>
      </option>
      <?php
    }
    ?>
  </select>
</div>

<style>
.option-highlight {
  background-color: #0d6efd !important;
  color: #fff !important;
  font-weight: bold;
  border-radius: 0px;
  padding: 3px 49.50px;
 }
</style>

<script>
$(document).ready(function() {
  $('#city').select2({
    allowClear: false,           
    minimumResultsForSearch: 0,  
    width: '200px',
    templateSelection: function (data) {
      return data.text || "";
    },
    templateResult: function (data) {
      if (!data.id) return data.text;

    
      if ($(data.element).hasClass('highlight')) {
        return $('<span class="option-highlight">' + data.text + '</span>');
      }
      return data.text;
    }
  }).val(null).trigger('change'); 
});
</script>
</body>
</html>
