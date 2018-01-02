<?php

function czyZalogowanyRedirect($redirect_url = null) {
    if (isset($_SESSION['login'])) {
        redirect($redirect_url);
    }
}

function redirect($adres) {
    header('Location:' . $adres);
    exit;
}

function zalogujUzytkownika($login, $haslo) {
    global $connection;
    
    $login = trim($login);
    $haslo = trim($haslo);
    
    $query = "SELECT * FROM uzytkownik WHERE login = '{$login}'";
    $select_uzytkownik = mysqli_query($connection, $query);
    
    if (mysqli_num_rows($select_uzytkownik) !== 1) {
        return false;
    }
    
    while ($row = mysqli_fetch_array($select_uzytkownik)) {
        $db_haslo = $row['haslo'];
        
        if (password_verify($haslo, $db_haslo)) {
            $_SESSION['login'] = $login;
            redirect('./gra.php');
        }
        else {
            return false;
        }
    }
}

function zarejestrujUzytkownika($login, $haslo) {
    global $connection;
    
    $login = mysqli_real_escape_string($connection, $login);
    $haslo = mysqli_real_escape_string($connection, $haslo);
    
    $haslo = password_hash($haslo, PASSWORD_BCRYPT, array('cost' => 12));
    
    $query = "INSERT INTO uzytkownik (login, haslo) VALUES('{$login}','{$haslo}')";
    mysqli_query($connection, $query);
    
}


function czyUżytkownikIstnieje($login) {
    global $connection;
    
    $query = "SELECT login FROM uzytkownik WHERE login = '$login'";
    $result = mysqli_query($connection, $query);
    
    return mysqli_num_rows($result) > 0;
    
}

function confirmQuery($result) {
    global $connection;
    
    if (!$result) {
        die("QUERY FAILED ." . mysqli_error($connection));
    }
}

function zalogowanyUzytkownik() {
    if(isset($_SESSION['login'])) {
        return $_SESSION['login'];
    }
    return false;
}

function mozeStworzycKolejne($login) {
  $limit_gier = 3;
  $id = pobierzIdUzytkownika($login);
  
  global $connection;
  
  $query = "SELECT COUNT(*) as ilosc FROM gra where host = $id AND status = 'aktywny'";
  $result = mysqli_query($connection, $query);
  $wynik = mysqli_fetch_assoc($result);
  
  return $wynik['ilosc'] < $limit_gier;
}

function stworzNowaGre($login) {
    global $connection;
  
    $uklad_poczatkowy = '[["","",""],["","",""],["","",""]]';
    
    $query = "SELECT id FROM uzytkownik WHERE login = '$login'";
    
    $result = mysqli_query($connection, $query);
    $wynik = mysqli_fetch_assoc($result);
    
    $query = "INSERT INTO gra(host, gosc, stan, status)
              VALUES ('$wynik[id]', null, '$uklad_poczatkowy', 'aktywny')";
    mysqli_query($connection, $query);
    
    return mysqli_insert_id($connection);
    
}

function pobierzLoginUzytkownikaPoId($id_uzytkownika) {
    global $connection;
    $query = "SELECT login FROM uzytkownik WHERE id = '$id_uzytkownika'";
    $result = mysqli_query($connection, $query);
    $wynik = mysqli_fetch_assoc($result);
 
    return $wynik['login'];
}

function pobierzIdUzytkownika($login) {
    global $connection;
    $query = "SELECT id FROM uzytkownik WHERE login = '$login'";
    $result = mysqli_query($connection, $query);
    $wynik = mysqli_fetch_assoc($result);
    
    return $wynik['id'];
}

function pobierzUzytkownikowGry($id_gry) {
    global $connection;
    $query = "SELECT * FROM gra WHERE id = '$id_gry'";
    $result = mysqli_query($connection, $query);
    $wynik = mysqli_fetch_assoc($result);
  
    $host = pobierzLoginUzytkownikaPoId($wynik['host']);
    $gosc = pobierzLoginUzytkownikaPoId($wynik['gosc']);
    
    return array('host' => $host, 'gosc' => $gosc);
}

function graczJestWGrze($uzytownik, $id_gra) {
    $gracze = pobierzUzytkownikowGry($id_gra);
    
    return in_array($uzytownik, $gracze, true);
}

function dolaczDoGry($zalogowany, $id_gry) {
    global $connection;
    $id_gracz = pobierzIdUzytkownika($zalogowany);
    
    $query = "UPDATE gra SET gosc = '{$id_gracz}' WHERE id = '{$id_gry}'";
    mysqli_query($connection, $query);
    
}

function pobierzStan($id) {
  global $connection;
  
  $query = "SELECT stan FROM gra WHERE id = '$id'";
  $result = mysqli_query($connection, $query);
  $wynik = mysqli_fetch_assoc($result);
  
  return $wynik['stan'];
}

function aktualizujStan($id, $row, $column) {
  global $connection;
  
  $query = "SELECT * FROM gra WHERE id = '$id'";
  $result = mysqli_query($connection, $query);
  
  $wynik = mysqli_fetch_assoc($result);
  $ilosc_ruchow = iloscRuchow($wynik['stan']);
  
  $nastepny = $ilosc_ruchow % 2 === 0 ? 'X' : 'O';
  
  $stan = json_decode(pobierzStan($id));
  $stan[$row][$column] = $nastepny;
  $stan = json_encode($stan);
  
  $query = "UPDATE gra SET stan = '{$stan}' WHERE id = '$id'";
  mysqli_query($connection, $query);

}

function mozeAktulizowac($login, $id, $row, $column) {
  global $connection;
  
  $query = "SELECT * FROM gra WHERE id = '$id'";
  $result = mysqli_query($connection, $query);
  $wynik = mysqli_fetch_assoc($result);
  
  $stan = json_decode($wynik['stan']);
  
  if($stan[$row][$column] !== '') {
    return false;
  }
  
  $id_uzytkownika = pobierzIdUzytkownika($login);
  $ilosc_ruchow = iloscRuchow($wynik['stan']);
  
  if($ilosc_ruchow % 2 === 0) {
    return $wynik['host'] === $id_uzytkownika;
  }
  else {
    return $wynik['gosc'] === $id_uzytkownika;
  }
}

function iloscRuchow($stan) {
  $array = json_decode($stan);
  
  $ilosc_ruchow = 0;
  foreach ($array as $wiersz) {
    foreach ($wiersz as $wartosc) {
      if($wartosc !== '') {
        $ilosc_ruchow++;
      }
    }
  }
  
  return $ilosc_ruchow;
}

function sprawdzWynik($id) {
  global $connection;
  
  $stan = pobierzStan($id);
  $array = json_decode($stan);
  
  $koniec = false;
  
  for($i = 0; $i < 3; $i++) {
    if(
      $array[$i] === ['X','X','X'] ||
      $array[$i] === ['O','O','O'] ||
      array_map(null, ...$array)[$i] === ['X','X','X'] ||
      array_map(null, ...$array)[$i] === ['O','O','O']
    ) {
      $koniec = true;
      break;
    }
  }
  
  if (
    [$array[0][0], $array[1][1],$array[2][2]] === ['X','X','X'] ||
    [$array[0][0], $array[1][1],$array[2][2]] === ['O','O','O'] ||
    [$array[2][0], $array[1][1],$array[0][2]] === ['X','X','X'] ||
    [$array[2][0], $array[1][1],$array[0][2]] === ['O','O','O']
  ) {
    $koniec = true;
  }
  
  if($koniec) {
    $query = "UPDATE gra SET status = 'koniec' WHERE id = '$id'";
    mysqli_query($connection, $query);
  }
}

function graAktywna($id) {
  global $connection;
  
  $query = "SELECT status FROM gra WHERE id = '$id'";
  $result = mysqli_query($connection, $query);
  $wynik = mysqli_fetch_assoc($result);
  
  return $wynik['status'] === 'aktywny';
}