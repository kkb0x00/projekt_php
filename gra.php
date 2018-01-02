<?php
include 'includes/header.php';
session_start();

if(!isset($_SESSION['login'])) {
  redirect('index.php');
}

$zalogowany = $_SESSION['login'];

if (isset($_POST['nowa_gra'])) {
  if(mozeStworzycKolejne($zalogowany)) {
    $id_gra = stworzNowaGre($zalogowany);
    redirect("gra.php?id=$id_gra");
  }
  else {
    redirect('dashboard.php?error=true');
  }
}

if (isset($_POST['dolacz'])) {
  $id_gra = $_POST['gra_id'];
  
  dolaczDoGry($zalogowany, $id_gra);
  redirect("gra.php?id=$id_gra");
}

if (!isset($_GET['id']) || !graczJestWGrze($zalogowany, $_GET['id'])) {
  redirect('dashboard.php');
}

$id_gra = $_GET['id'];
$gracze = pobierzUzytkownikowGry($id_gra);

?>

<script src="/js/jquery.js"></script>
<script>
  function pobierzStan() {
    $.get("stan.php?id=" + <?php echo $id_gra; ?>, function(res) {
      let json = JSON.parse(res);

      let stan = JSON.parse(json[0]);
      let graAktywna = json[1];
      let gracz = json[2];
      
      for (let i in stan) {
        for(let j in stan[i]) {
          $(`#wartosc_${i}_${j}`).html(stan[i][j]);
        }
      }
      
      $('input').removeClass('btn-success');
      $(`#${gracz}`).addClass('btn-success');
      
      if(!graAktywna) {
        $('#stan_gry').html('Gra została zakończona!');
      }
    });
  }
  
  setInterval(pobierzStan, 2000);
</script>

<script>
  $(document).ready(function() {
    $("table td").click(function() {

      let row_num = parseInt($(this).parent().index());
      let column_num = parseInt($(this).index());

      $.ajax({
        type: "POST",
        url: "stan.php",
        data: {
          id: <?php echo $id_gra; ?>,
          row_num: row_num,
          column_num: column_num
        }
      });
    });
  });
</script>
<p>Twój login: <b><?php echo $zalogowany?></b>.</p>
<a href="dashboard.php">Wróć do widoku gier. </a>

<div class="mt-5">
  <div class="text-center">
      <input id="host" class="text-center btn" readonly="readonly" onclick="this.blur();" value="Gracz: <?php echo $gracze['host'];?>  -  X">
      <input id="gosc" class="text-center btn" readonly="readonly" onclick="this.blur();" value="Gracz: <?php echo $gracze['gosc'] ?: '....'?>  -  O">
  </div>
  
  <div class="d-flex justify-content-around mt-3">
    <div class="col-md-4">
      <form id="plansza" method="post" action="stan.php">
        <table class="table table-bordered">
          <tbody>
          <tr>
            <td id="wartosc_0_0" class="text-center"></td>
            <td id="wartosc_0_1" class="text-center"></td>
            <td id="wartosc_0_2" class="text-center"></td>
          </tr>
          <tr>
            <td id="wartosc_1_0" class="text-center"></td>
            <td id="wartosc_1_1" class="text-center"></td>
            <td id="wartosc_1_2" class="text-center"></td>
          </tr>
          <tr>
            <td id="wartosc_2_0" class="text-center"></td>
            <td id="wartosc_2_1" class="text-center"></td>
            <td id="wartosc_2_2" class="text-center"></td>
          </tr>
          </tbody>
        </table>
        <input type="submit" hidden="hidden" name="zagraj">
      </form>
    </div>
  </div>
</div>

<p id="stan_gry" class="text-center"></p>
