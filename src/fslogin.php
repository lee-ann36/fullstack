<?php
session_start();

$servername = "mysql";
$username = "root";
$password = "password";

  try {
    $conn = new mysqli($servername, $username, $password, "tools4ever");
  } catch ( mysqli_sql_exception) {
    echo"Could not connect";
  }

  if (isset($_POST['inloggen'])) {
  $gebruikersnaam = $_POST['gebruikersnaam'];
  $wachtwoord = $_POST['wachtwoord'];

    //gebruikers ophalen uit de database
    $stmt = $conn->prepare("SELECT * FROM medewerkers WHERE email = ?");
    $stmt->bind_param("s", $gebruikersnaam);
    $stmt->execute();
    $result = $stmt->get_result();

    //controleren en doorversturen 
    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();

        if (password_verify($wachtwoord, $user['password'])) {
        $_SESSION['gebruikersnaam'] = $gebruikersnaam;
        $_SESSION['rol'] = $user['rol'];
        header("Location: home.php");
        exit();      
        } else {
            echo "<p style='color:red;'>Wachtwoord is onjuist.</p>";
        }
    } else {
        echo "<p style='color:red;'>Gebruikersnaam niet gevonden.</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>inlog</title>
<link rel="stylesheet" href="css/fsinlog.css">
</head>

<body>

<form method="POST">
  <h2>Inloggen</h2>

  <label for="gebruikersnaam">Gebruikersnaam</label>
  <input type="text" id="gebruikersnaam" name="gebruikersnaam" required>

  <label for="wachtwoord">Wachtwoord</label>
  <input type="password" id="wachtwoord" name="wachtwoord" required>

  <button type="submit" name="inloggen">Inloggen</button>
</form>

</body>
</html>