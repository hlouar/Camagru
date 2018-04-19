<?php

session_start();
$servername = "mysql:dbname=camagru;host=localhost:3307";
$username = "root";
$password = "rootroot";

try {
  $dbh = new PDO($servername, $username, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
} catch (PDOException $e) {
  echo 'Connexion échouée : ' . $e->getMessage();
}

if ($_POST["email"] != "") {
  $req = $dbh->prepare("SELECT * FROM account WHERE username = :username");
  $req->bindParam("username", $_POST["actualUsername"]);
  $req->execute();
  $row = $req->fetch(PDO::FETCH_ASSOC);
  if ($row && $row["email"] != $_POST["email"]) {
    echo "This email is already used";
    return ;
  }

  $req = $dbh->prepare("UPDATE account SET email = :email WHERE username = :actualUser");
  $req->bindParam(':email', $_POST["email"]);
  $req->bindParam(':actualUser', $_POST["actualUsername"]);
  $req->execute();
}

if ($_POST["username"] != "") {
  $req = $dbh->prepare("SELECT * FROM account WHERE username = :username");
  $req->bindParam("username", $_POST["username"]);
  $req->execute();
  $row = $req->fetch(PDO::FETCH_ASSOC);
  if ($row && $_POST["actualUsername"] != $_POST["username"]) {
    echo "This username is already used";
    return ;
  }

  $req = $dbh->prepare("UPDATE account SET username = :username WHERE username = :actualUser");
  $req->bindParam(':username', $_POST["username"]);
  $req->bindParam(':actualUser', $_POST["actualUsername"]);
  $req->execute();
  $_SESSION["pseudo"] = $_POST["username"];
}

if ($_POST["oldPassword"] != "" && $_POST["newPassword"] != ""){
  $req = $dbh->prepare("SELECT * FROM account WHERE username = :username");
  $req->bindParam("username", $_POST["username"]);
  $req->execute();
  $row = $req->fetch(PDO::FETCH_ASSOC);

  $hash_newpassword = password_hash($_POST["newPassword"], PASSWORD_BCRYPT);
  if (!password_verify($_POST["oldPassword"], $row["password"])) {
    echo "Wrong password";
    return ;
  }
  else {
    if (!(preg_match('/[A-Z]/', $_POST["newPassword"]))) {
      echo "The password need an uppercase";
      return;
    }
    if (!(preg_match('/[0-9]/', $_POST["newPassword"]))) {
      echo "The password need a number";
      return;
    }
    if (strlen($_POST["newPassword"]) < 5) {
      echo "The password need to have at least 5 caracters";
      return;
    }

    $req = $dbh->prepare("UPDATE account SET password = :password WHERE username = :username");
    $req->bindParam(':password', $hash_newpassword);
    $req->bindParam(':username', $_POST["username"]);
    $req->execute();
  }
}
echo $_POST["username"];
?>