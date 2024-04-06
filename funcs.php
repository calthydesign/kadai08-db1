<?php
//エラーメッセージ
ini_set('display_errors', '1');
error_reporting(E_ALL);

//key取得
require_once 'key.php';

// OpenWeatherAPIへのリクエストURL
$url = "http://api.openweathermap.org/data/2.5/weather?q=$CITY&appid=$API_KEY&units=metric&lang=ja";

// cURLを使ってOpenWeatherAPIからデータを取得
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
curl_close($ch);

// JSON形式のレスポンスをデコード
$data = json_decode($response, true);

// 天気情報を取得
$weather = $data['weather'][0]['description'];


//1. POSTデータ取得
$weather = $_POST["weather"] ?? $data['weather'][0]['description'];
$conditions = $_POST["conditions"] ?? '';
$symptoms = $_POST["symptoms"] ?? '';
$memo = $_POST["memo"] ?? '';
// var_dump($_POST); // formの送信方法に合わせて出力
// exit();


//2. DB接続します(local)
// try {
  //Password:MAMP='root',XAMPP=''  PDO＝php data object
//   $pdo = new PDO('mysql:dbname=kadai08;charset=utf8;host=localhost','root',''); 
// } catch (PDOException $e) {
//   exit('DB_CONECT:'.$e->getMessage());
// }

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


//３．データ登録SQL作成,このとおりかくこと！
$sql = "INSERT INTO kadai08_table(indate,weather,conditions,symptoms,memo)VALUES(sysdate(), :weather, :conditions, :symptoms, :memo);";
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':weather',   $weather,   PDO::PARAM_STR);  //Integer（数値の場合 PDO::PARAM_INT) 
$stmt->bindValue(':conditions', $conditions, PDO::PARAM_STR);  //Integer（数値の場合 PDO::PARAM_INT)
$stmt->bindValue(':symptoms',  $symptoms,  PDO::PARAM_STR);  //Integer（数値の場合 PDO::PARAM_INT)
$stmt->bindValue(':memo',      $memo,      PDO::PARAM_STR);  //Integer（数値の場合 PDO::PARAM_INT) 
$status = $stmt->execute();//ここで実行！true or falseが返ってくる

//４．データ登録処理後
if($status==false){
  //SQL実行時にエラーがある場合（エラーオブジェクト取得して表示）＝SQL失敗
  $error = $stmt->errorInfo();
  exit("SQL_ERROR:".$error[2]);//自分のわかる言葉をセット
}else{
  //５．index.phpへリダイレクト
header("Location: index.php");
exit();
}
?>