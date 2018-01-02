<?php
session_start();
include 'includes/header.php';

czyZalogowanyRedirect('/dashboard.php');
$blad = false;

if (isset($_POST['zaloguj'])) {
    if (isset($_POST['login'], $_POST['haslo'])) {
        if(!zalogujUzytkownika($_POST['login'], $_POST['haslo'])) {
          $blad = true;
        }
    } else {
        redirect('/index.php');
    }
}

?>

<form method="post">
  <input name="login">
  <input name="haslo" type="password">
  <input name="zaloguj" type="submit" value="Zaloguj się">
  
  <?php if($blad): ?>
    <p class="alert alert-danger col-md-4 mt-2">Nieprawidłowy login lub hasło!</p>
  <?php endif; ?>
</form>

<p>Nie masz konta? <a href="rejestracja.php">Zarejestruj się.</a></p>
</body>

</html>


<?php include 'includes/footer.php';?>