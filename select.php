<?php
//エラーメッセージ
ini_set('display_errors', '1');
error_reporting(E_ALL);


//2. DB接続します(local)
// try {
  //Password:MAMP='root',XAMPP=''  PDO＝php data object
//   $pdo = new PDO('mysql:dbname=kadai08;charset=utf8;host=localhost','root',''); 
// } catch (PDOException $e) {
//   exit('DB_CONECT:'.$e->getMessage());
// }

//key取得
require_once 'key.php';

//DB接続(さくらサーバー)
try {
  $server_info ='mysql:dbname='.$db_name.';charset=utf8;host='.$db_host;
  $pdo = new PDO($server_info, $db_id, $db_pw);
} catch (PDOException $e) {
  exit('DB Connection Error:' . $e->getMessage());
}

//SQLエラー
function sql_error($stmt)
{
    //execute（SQL実行時にエラーがある場合）
    $error = $stmt->errorInfo();
    exit('SQLError:' . $error[2]);
}


//２．データ登録SQL作成
// $sql = "SELECT * FROM kadai08_table ORDER BY id DESC;";

//検索機能
if (isset($_GET['search'])) {
  $search = $_GET['search'];
  $sql = "SELECT * FROM kadai08_table WHERE symptoms LIKE :search ORDER BY id DESC";
  $stmt = $pdo->prepare($sql);
  $stmt->bindValue(':search', '%' . $search . '%', PDO::PARAM_STR);
} else {
  $sql = "SELECT * FROM kadai08_table ORDER BY id DESC";
  $stmt = $pdo->prepare($sql);
}
$status = $stmt->execute();//true or false

//３．データ表示
if($status==false) {
  //execute（SQL実行時にエラーがある場合）
  $error = $stmt->errorInfo();
  exit("SQL_ERROR:".$error[2]);//エラー表示
}

//全データ取得（配列で受け取る）fetchAllはすべてのデータを取る関数
$values =  $stmt->fetchAll(PDO::FETCH_ASSOC); //PDO::FETCH_ASSOC[カラム名のみで取得できるモード]
//JSONに値を渡す場合に使う
// $json = json_encode($values,JSON_UNESCAPED_UNICODE);

?>


<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>一覧</title>
<link href="reset.css" rel="stylesheet">
<link rel="stylesheet" href="style.css">
</head>

<body id="main">
<!-- Head[Start] -->
<header>
<h1>Records</h1>
</header>

<!-- Main[Start] -->
<form method="get" id="searchForm">
    <input type="text" name="search" placeholder="症状別検索">
    <button id="searchBtn" type="submit">検索</button>
</form>
<div class="container jumbotron">
  <table>
    <?php foreach($values as $value){ ?>
      <tr>
        <td>
          <div class="row">
            <div class="col"><?=$value["indate"]?></div>
            <div class="col"><?=$value["weather"]?></div>
          </div>
        </td>
        <td>
          <div class="vertical-align">
            <div><span>今日の調子：</span><?=$value["conditions"]?></div>
            <div><span>症状：</span><?=$value["symptoms"]?></div>
            <div><span>メモ：</span><?=$value["memo"]?></div>
          </div>
        </td>
      </tr>
    <?php } ?>
  </table>
</div>

    

<!-- Main[End] -->
<?php include("btn.html"); ?>


<!-- <script>
  //JSON受け取り
  $a = '<?=$json?>';
  const obj = JSON.parse($a);//オブジェクトに変換
  console.log(obj);


</script> -->
</body>
</html>
