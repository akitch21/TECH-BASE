<!DOCTYPE html>
<html lang="ja">
  <head>
    <meta charset="UTF-8">
    <title>mission_5-1</title>
  </head>
  <body>
<?php
    
$dsn = 'mysql:dbname=データベース名;host=localhost';
$user = 'ユーザ名';
$password = 'パスワード';
$pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
    
// データベース内にテーブルを作成
$sql = "CREATE TABLE IF NOT EXISTS tb_m5"
    ." ("
    . "id INT AUTO_INCREMENT PRIMARY KEY,"
    . "name char(32),"
    . "comment TEXT,"
    . "postedAt TEXT,"
    . "password TEXT"    
    .");";
$stmt = $pdo->query($sql);

// 投稿機能
if (!empty($_POST['name']) && !empty($_POST['comment']) && !empty($_POST['password'])) {
    $name = $_POST['name'];
    $comment = $_POST['comment'];
    $password = $_POST['password'];
    $postedAt = date("Y年m月d日 H:i:s");

    if (empty($_POST['editNO'])) {
        // 新規投稿
        $sql = "INSERT INTO tb_m5 (name, comment, postedAt, password) VALUES (:name, :comment, :postedAt, :password)";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':name', $name, PDO::PARAM_STR);
        $stmt->bindParam(':comment', $comment, PDO::PARAM_STR);
        $stmt->bindParam(':postedAt', $postedAt, PDO::PARAM_STR);
        $stmt->bindParam(':password', $password, PDO::PARAM_STR);
        $stmt->execute();
        echo "test";
    } elseif (!empty($_POST['editNO']) && !empty($_POST["password"])) {
        $id =  $_POST['editNO'];
         $editNO = $_POST['editNO'];
         $edit_password = $_POST['password'];
         $edit_name = $_POST['name'];
         $edit_comment = $_POST['comment'];

    $sql = 'UPDATE tb_m5 SET name=:name,comment=:comment WHERE id=:id';
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':name', $edit_name, PDO::PARAM_STR);
    $stmt->bindParam(':comment', $edit_comment, PDO::PARAM_STR);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();


    }
}

// 削除機能
if (!empty($_POST['dnum']) && !empty($_POST['delete_password'])) {
    $delete = $_POST['dnum'];
    $delete_password = $_POST['delete_password'];

    $sql = 'SELECT * FROM tb_m5 WHERE id=:id';
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':id', $delete, PDO::PARAM_INT); 
    $stmt->execute();
    $results = $stmt->fetchAll();

    foreach ($results as $row) {
        if ($delete_password == $row['password']){
            $sql = 'DELETE FROM tb_m5 WHERE id=:id';
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':id', $delete, PDO::PARAM_INT);
            $stmt->execute();
        } else {
            echo "パスワードが違います<br>";
        }
    }  
}

// 編集選択機能
if (!empty($_POST['edit']) && isset($_POST['edit_password'])) {
    $edit = $_POST['edit'];
    $edit_password = $_POST['edit_password'];

    $sql = 'SELECT * FROM tb_m5 WHERE id = :id AND password = :password';
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':id', $edit, PDO::PARAM_INT);
    $stmt->bindParam(':password', $edit_password, PDO::PARAM_STR);
    $stmt->execute();
    $results = $stmt->fetchAll();


    foreach ($results as $row) {
        if($edit == $row['id'] && $edit_password == $row['password']){
        //$rowの中にはテーブルのカラム名が入る
        $editnumber = $row['id'];
        $editname = $row['name'];
        $editcomment = $row['comment'];
        }
    }
}
?>
[投稿フォーム]
    <form action="mission_5-1.php" method="post">
      <input type="text" name="name" placeholder="名前" value="<?php if(isset($editname) && isset($_POST['edit'])) { echo $editname; } ?>"><br>
      <input type="text" name="comment" placeholder="コメント" value="<?php if(isset($editcomment) && isset($_POST['edit'])) { echo $editcomment; } ?>"><br>
      <input type="hidden" name="editNO" value="<?php if(isset($editnumber)) { echo $editnumber; } ?>">
      <input type="text" name="password" placeholder="パスワード">
      <input type="submit" name="submit" value="送信">
    </form>
[削除フォーム]
    <form action="mission_5-1.php" method="post">
      <input type="number" name="dnum" placeholder="削除対象番号">
      <input type="text" name="delete_password" placeholder="パスワード">
      <input type="submit" name="delete" value="削除">
    </form>
[編集フォーム]
    <form action="mission_5-1.php" method="post">
      <input type="number" name="edit" placeholder="編集対象番号">
      <input type="text" name="edit_password" placeholder="パスワード">
      <input type="submit" value="編集">
    </form>
    
<br>

    <?php
    $sql = 'SELECT * FROM tb_m5';
    $stmt = $pdo->query($sql);
    $results = $stmt->fetchAll();
    foreach ($results as $row){
        //$rowの中にはテーブルのカラム名が入る
        echo $row['id'].',';
        echo $row['name'].',';
        echo $row['comment'].',';
        echo $row['postedAt'].'<br>';
    echo "<hr>";
    }
    ?>
  </body>
</html>
