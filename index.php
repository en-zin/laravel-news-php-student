<?php
//変数の準備
$FILE = './data.txt'; //保存ファイル名
$id = uniqid(); //ユニークなIDを自動生成
$text = ''; //入力テキスト
$DATA = []; //一回分の投稿の情報を入れる
$BOARD = []; //全ての投稿の情報を入れる
$error_message = [];

//$FILEというファイルが存在しているとき
//file_exists => ファイルチェック
//json_decode => encodeされたデータを復元する
if (file_exists($FILE)) {
  //ファイルを読み込む
  $BOARD = json_decode(file_get_contents($FILE));
}

//$_SERVERは送信されたサーバーの情報を得る
//REQUEST_METHODはフォームからのリクエストのメソッドがPOSTかGETか判断する
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  //$_POSTはHTTPリクエストで渡された値を取得する
  //リクエストパラメーターが空でなければ
  //emptyは中身がなければture、あればfalseを返す
  if (!empty($_POST['txt']) && !empty($_POST['title'])) {
    if (mb_strlen($_POST['title']) > 30) {
      $error_message[] = 'タイトルは30文字以下でお願いします。';
    }else{
    //$textに送信されたテキストを代入
    $title = $_POST['title'];
    $text = $_POST['txt'];

    //新規データ
    //新規データを配列にする
    $DATA = [$id, $title, $text];

    //この時$BOARDには.txtのデータが全て入っているため新たに$DATAを追加することになる
    //array_push($BOARD,$DATA)と書いても良い
    $BOARD[] = $DATA;

    //全体配列をファイルに保存する
    //json_encode =>  JSON形式にした文字列を返します。  これをしないとただの配列だよarrayが保存される。encodeすることで配列として保存される
    //JSON_UNESCAPED_UNICODEでJdonを保存する際エスケープする
    file_put_contents($FILE, json_encode(($BOARD),JSON_UNESCAPED_UNICODE));

    //header()で指定したページにリダイレクト
    //今回は今と同じ場所にリダイレクト（つまりWebページを更新）
    header('Location: ' . $_SERVER['SCRIPT_NAME']);
    //プログラム終了
    exit;
    }
    
  } else {
    if (empty($_POST['title'])) $error_message[] = 'タイトルは必須です。';
    if (empty($_POST['txt'])) $error_message[] = "記事は必須です。";
  }
}
?>

<!DOCTYPE html>
<html lang="ja">

<head>
  <meta name="viewport" content="width=device-width, initial-scale= 1.0">
  <meta http-equiv="content-type" charset="utf-8">
  <link rel='stylesheet' href='./css/index.css' type="text/css">
  <title>Laravel news</title>
</head>

<body>
  <h1 class='title'>Laravel News</h1>

  <section class="main">
    <h2 class="subTitle">さぁ、最新のニュースをシェアしましょう</h2>

    <!-- Errorメッセージ -->
    <ul>
      <?php foreach ($error_message as $error) : ?>
        <li>
          <?php echo $error ?>
        </li>
      <?php endforeach; ?>
    </ul>

    <!--投稿-->
    <form method="post" class="form" onsubmit="return submitCheckFunction()">
      <div class='titleContainer'>
        <p class='nameFlex'>title: </p>
        <input type='text' name='title' class="inputFlex">
      </div>
      <div class='articleContainer'>
        <p class='nameFlex'>記事: </p>
        <textarea rows="10" cols="60" name="txt" class="inputFlex articleInput"></textarea>
      </div>
      <div class="submitContainer">
        <input type="submit" value="投稿" class="submitStyle">
      </div>
    </form>

    <hr>

    <!-- content -->
    <div class='Container'>
      <?php foreach (array_reverse($BOARD) as $DATA) : ?>
        <div class="content">
          <p class="articleTitle">
            <?php echo $DATA[1]; ?>
          </p>
          <p class="articleText">
            <?php echo $DATA[2]; ?>
          </p>
          <p class='routingStyle'><a href='article.php?id=<?php echo $DATA[0]; ?>'>記事全文・コメントを見る</a></p>
        </div>
      <?php endforeach; ?>
    </div>
  </section>

  <script type="text/javascript" src="./js/index.js"></script>
</body>

</html>