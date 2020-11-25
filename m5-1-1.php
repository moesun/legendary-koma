<html>
    <head>
        <meta name="viewport" 
              content=
                "width=300, height=400,
                initial-scale=2.0, 
                minimum-scale=2.0, maximum-scale=3.0,
                user-scalable=yes"
        >
        <meta charset="utf-8">
        <title>掲示板</title>

    </head>
    <body>
    <body bgcolor="#99CCCC" text="#FFFFFF">
    <h1> ◎けいじばん◎</h1>
    
<?php
	// DB接続設定
	$dsn =  'データベース名';
	$user = 'ユーザー名';
	$password = 'パスワード';
	$pdo = new PDO($dsn, $user, $password, 
	 array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));

   //テーブル作成
    $sql = "CREATE TABLE IF NOT EXISTS m5_01"
	." ("
	. "id INT AUTO_INCREMENT PRIMARY KEY,"
	. "name char(32),"
	. "comment TEXT,"
	. "date TEXT,"
	. "pass TEXT"
	.");";
	$stmt = $pdo->query($sql);
	
	//新規投稿なら
	if(!empty($_POST["name"]) && !empty($_POST["comment"]) &&
        !empty($_POST["pass"]) && 
        empty($_POST["num_mark"])){
            
    $sql = $pdo -> prepare(
      "INSERT INTO m5_01(name, comment, date, pass) 
        VALUES (:name, :comment, :date, :pass)");
	$sql -> bindParam(':name', $name, PDO::PARAM_STR);
	$sql -> bindParam(':comment', $comment, PDO::PARAM_STR);
	$sql -> bindParam(':date', $date, PDO::PARAM_STR);
	$sql -> bindParam(':pass', $pass, PDO::PARAM_STR);
	$name = $_POST["name"];
	$comment = $_POST["comment"]; 
	$date = date("Y年m月d日 H:i:s");
	$pass = $_POST["pass"];
	$sql -> execute();
    
    
    //削除なら
    }elseif(!empty($_POST["num_delete"]) && 
             !empty($_POST["delete_pass"])){
    
    $id = $_POST["num_delete"];
    $delete_pass = $_POST["delete_pass"];
    
    $sql = 'SELECT * FROM m5_01 WHERE id=:id ';
    $stmt = $pdo->prepare($sql);                  
    $stmt->bindParam(':id', $id, PDO::PARAM_INT); 
    $stmt->execute();                            
    $results = $stmt->fetchAll(); 
    foreach ($results as $row){
        if($row['pass'] == $delete_pass){
            $sql = 'delete from m5_01 where id=:id';
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
        }
    }
    
    
    //編集なら
    }elseif(!empty($_POST["num_edit"])
            &&!empty($_POST["edit_pass"])){
	
	$id = $_POST["num_edit"];
	$edit_pass = $_POST["edit_pass"];
	
	$sql = 'SELECT * FROM m5_01 WHERE id=:id ';
    $stmt = $pdo->query($sql);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();    
    $results = $stmt->fetchAll();
    
    foreach ($results as $row){
        if($row['pass'] == $edit_pass){
            $num2 = $row['id'];
            $name2 = $row['name'];
            $comment2 = $row['comment'];
            $pass2 = $row['pass'];
        }
    }
        
}
        



?>

<head>
  <meta charset="UTF-8">
</head>
<form action="" method="post">
            <b>✏投稿✏</b>
        <br>
        <input type="text" name="name" placeholder="名前"
          value="<?php if(isset($_POST["edit"])){
                                echo $name2;
                            }?>">
        <input type="text" name="comment" placeholder="コメント"
          value="<?php if(isset($_POST["edit"])){
                                echo $name2;
                            }?>">
        <br>
        <input type="text" name="pass" placeholder="パスワード"
          value="<?php if(isset($_POST["edit"])){
                                echo $pass2;
                            }?>">
        <input type="hidden" name="num_mark"
            value = "<?php if(isset($_POST["edit"])){
                                echo $name2;
                            }?>">
        <input type="submit" name="submit">
        <br><br>
        
        <b>🗑削除🗑</b>
        <br>
        <input type="number" name="num_delete" placeholder="削除番号">
        <br>
        <input type="text" name="delete_pass" placeholder="パスワード">
        <input type="submit" name="delete" value="削除">
        <br><br>
        
        <b>🛠編集🛠</b>
        <br>
        <input type="number" name="num_edit" placeholder="編集番号">
        <br>
        <input type="text" name="edit_pass" placeholder="パスワード">
        <input type="submit" name="edit" value="編集">
        
 </form>
 
 <?php
    //編集モードなら
    if(!empty($_POST["name"]) && !empty($_POST["comment"])
       && !empty($_POST["pass"]) && 
       !empty($_POST["num_mark"])){
       
    $id = $_POST["num_mark"];
    $name = $_POST["name"];
    $comment = $_POST["comment"];
    $date = date("Y年m月d日 H:i:s");
    $pass = $_POST["pass"];
    
    $sql = 'UPDATE m5_01 SET
    name=:name,comment=:comment,date=:date,pass=:pass
    WHERE id=:id';
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':name', $name, PDO::PARAM_STR);
    $stmt->bindParam(':comment', $comment, PDO::PARAM_STR);
    $stmt->bindParam(':date', $date, PDO::PARAM_STR);
    $stmt->bindParam(':pass', $pass, PDO::PARAM_STR);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();    
    
    }
    
    if(isset($_POST["submit"]) && empty($_POST["name"])){
       echo "<br>名前を入力してください<br>";
   }elseif(isset($_POST["submit"]) && empty($_POST["comment"])){
       echo "<br>コメントを入力してください<br>";
   }elseif(isset($_POST["submit"]) && empty($_POST["pass"])){
       echo "<br>パスワードを入力してください<br>";
   }elseif(isset($_POST["delete"]) && empty($_POST["num_delete"])){
       echo "<br>削除番号を入力してください<br>";
   }elseif(isset($_POST["delete"]) && empty($_POST["delete_pass"])){
       echo "<br>パスワードを入力してください<br>";
   }elseif(isset($_POST["edit"]) && empty($_POST["num_edit"])){
       echo "<br>編集番号を入力してください<br>";
   }elseif(isset($_POST["edit"]) && empty($_POST["edit_pass"])){
       echo "<br>パスワードを入力してください<br>";
   }
   
   
   echo "<br> <b>⇩投稿一覧⇩</b> <br><br>";
   
    $sql = 'SELECT * FROM m5_01';
    $stmt = $pdo->query($sql);
    $results = $stmt->fetchAll();
    foreach ($results as $row){
        //$rowの中にはテーブルのカラム名が入る
        echo $row['id'].'．';
        echo $row['name'];
        echo '「'.$row['comment'].'」';
        echo $row['date'].'<br>';
    }

?>