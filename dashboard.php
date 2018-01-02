<?php
session_start();
include 'includes/header.php';

if(!isset($_SESSION['login'])) {
  redirect('index.php');
}

$zalogowany = zalogowanyUzytkownik();
$id_gracza = pobierzIdUzytkownika($zalogowany);
?>
<p>Witaj <b><?php echo $zalogowany?></b>.</p>
<form method="post" action="gra.php">
  <label>Stwórz nową grę.</label>
  <input name="nowa_gra" type="submit" value="Stwórz grę.">
</form>

<?php if(isset($_GET['error']) && $_GET['error'] === 'true'): ?>
  <p class="alert alert-danger">Masz za dużo niedokończonych gier!<br>
  Dokończ aktualne gry aby móc zagrać w kolejne.
  </p>
<?php endif; ?>

<a href="logout.php">Wyloguj się</a>
  
<div class="d-flex flex-row justify-content-around">
  <div class="col-md-4">
    <h2>Twoje gry:</h2>
    <table class="table table-bordered">
      <thead>
      <tr>
        <th class="text-center">Host</th>
        <th class="text-center">Gosc</th>
        <th class="text-center">Wejdź</th>
      </tr>
      </thead>
    
      <tbody>
    
      <?php
    
      $query = "SELECT * FROM gra WHERE (host = '$id_gracza' OR gosc = '$id_gracza') AND status = 'aktywny'";
      $select_gry = mysqli_query($connection, $query);
    
      while ($row = mysqli_fetch_assoc($select_gry)) {
          $gra_id = $row['id'];
        
          $host = pobierzLoginUzytkownikaPoId($row['host']);
          $gosc = pobierzLoginUzytkownikaPoId($row['gosc']);
        
          echo "<tr>";
          echo "<td class='text-center'>$host</td>";
          echo "<td class='text-center'>$gosc</td>";
          echo "<td class='text-center'><a href='gra.php?id=$gra_id'>Kontynuuj grę.</a></td>";
          echo "</tr>";
      }
    
      ?>
    
      </tbody>
    </table>
  </div>
  
  <div class="col-md-4">
    <h2>Dołącz do gier:</h2>
    <table class="table table-bordered">
      <thead>
      <tr>
        <th class='text-center'>Host</th>
        <th class='text-center'>Gosc</th>
      </tr>
      </thead>
      
      <tbody>
      
      <?php
      
      $query = "SELECT * FROM gra WHERE host != '$id_gracza' AND gosc IS NULL AND status = 'aktywny'";
      $select_gry = mysqli_query($connection, $query);
      
      while ($row = mysqli_fetch_assoc($select_gry)) {
          $gra_id = $row['id'];
          
          $host = pobierzLoginUzytkownikaPoId($row['host']);
          $gosc = pobierzLoginUzytkownikaPoId($row['gosc']);
          
          echo "<tr>";
          echo "<td class='text-center'>$host</td>";
          echo "
            <td class='text-center'>
              <form method=\"post\" action=\"gra.php\">
                <input type=\"hidden\" name=\"gra_id\" value=\"${gra_id}\">
                <input type=\"submit\" name=\"dolacz\" class=\"btn btn-success btn-sm\" value=\"Dołącz\">
              </form>
            </td>";
          echo "</tr>";
      }
      ?>
      </tbody>
    </table>
  </div>
</div>

<?php include 'includes/header.php'; ?>