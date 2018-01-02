<?php
session_start();
include_once 'includes/db.php';
include_once 'includes/funkcje.php';

if(isset($_GET['id'])) {
  $id = $_GET['id'];
  $stan = pobierzStan($id);
  
  $aktywny_gracz = iloscRuchow($stan) % 2 === 0 ? 'host' : 'gosc';
  
  echo json_encode([$stan, graAktywna($id), $aktywny_gracz]);
}

if(isset($_POST['id'])) {
  $id = $_POST['id'];
  $row =  $_POST['row_num'];
  $column = $_POST['column_num'];
  
  $login = $_SESSION['login'];
  
  if (graAktywna($id) && mozeAktulizowac($login, $id, $row, $column)) {
    aktualizujStan($id, $row, $column);
    sprawdzWynik($id);
  }
}