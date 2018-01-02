<?php
session_start();
include 'includes/header.php';

$error = false;

if (isset($_POST['zarejestruj'])) {
  if(!empty($_POST['login']) && !empty($_POST['haslo'])) {
    $login = trim($_POST['login']);
    $haslo = trim($_POST['haslo']);
    
    if (czyUżytkownikIstnieje($login)) {
      $error = true;
    }
    else {
      zarejestrujUzytkownika($login, $haslo);
      zalogujUzytkownika($login, $haslo);
    }
  }
}

?>

<form method="post">
    <input name="login">
    <input name="haslo" type="password">
  
    <?php if($error) :?>
      <p class="alert alert-danger">Login już istnieje. Użyj nowego.</p>
    <?php endif;?>
  
    <input name="zarejestruj" type="submit" value="Zarejestruj się">
</form>

<p>Masz już konto? <a href="index.php">Zaloguj się.</a></p>

<?php include 'includes/footer.php'; ?>