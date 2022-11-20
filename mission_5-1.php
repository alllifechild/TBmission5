 <html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>mission_5-1</title>
</head>
<body>
    <?php
        
    
        // データベースに接続
        $dsn = 'mysql:dbname=tb240452db;host=localhost';
        $user = 'tb-240452';
        $password = 'VE4ffz36Tu';
        $pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
        
        //データベース内にテーブルを作成
        $sql = "CREATE TABLE IF NOT EXISTS tbfinal"
        ." ("
        . "id INT AUTO_INCREMENT PRIMARY KEY,"
        . "name char(32),"
        . "comment TEXT,"
        . "time datetime,"
        . "pass TEXT"
        .");";
        $stmt = $pdo->query($sql);
        
        
        //新規投稿
        if ($_SERVER['REQUEST_METHOD'] === 'POST'){
            if (isset($_POST['submit1'])){
                if (!empty($_POST["name"]) && !empty($_POST["comment"]) && empty($_POST["edit_n"]) && !empty($_POST["pass_new"])){ 
                    
                    $sql = $pdo -> prepare("INSERT INTO tbfinal (name, comment, time, pass) VALUES (:name, :comment, :time, :pass)");
                    $sql -> bindParam(':name', $name, PDO::PARAM_STR);
                    $sql -> bindParam(':comment', $comment, PDO::PARAM_STR);
                    $sql -> bindParam(':time', $time, PDO::PARAM_STR);
                    $sql -> bindParam(':pass', $pass, PDO::PARAM_STR);
                    
                    $name = $_POST["name"];
                    $comment = $_POST["comment"];
                    $pass = $_POST["pass_new"];
                    $time = date("Y/m/d H:i:s");
                    $sql -> execute();
                }
            }
        }
        
        
        
        //投稿消去
        if ($_SERVER['REQUEST_METHOD'] === 'POST'){
            if (isset($_POST['submit2'])){
                if (!empty($_POST["del"]) && !empty($_POST["pass_del"])){  
                    $del = $_POST["del"];
                    $pass_del = $_POST["pass_del"];
                    
                    $sql = 'SELECT * FROM tbfinal where id=:del';
                    $stmt = $pdo->prepare($sql);
                    $stmt->bindParam(':del', $del, PDO::PARAM_INT);
                    $stmt->execute();
                    $results = $stmt->fetch();
                    
                    $id = $results['id'];
                    $comment = $results['comment'];
                    $pass = $results['pass'];
                    
                    echo $id;
                    echo $pass;
                    
                    if ($id==$del && $pass==$pass_del){
                        $sql = 'SELECT * FROM tbfinal';
                        $stmt = $pdo->query($sql);
                        $results = $stmt->fetchAll();
                        $sql = 'DROP TABLE tbfinal';
                        $stmt = $pdo->query($sql);
                        // テーブルの作成
                        $sql = "CREATE TABLE IF NOT EXISTS tbfinal"
                        ." ("
                        . "id INT AUTO_INCREMENT PRIMARY KEY,"
                        . "name char(32),"
                        . "comment TEXT,"
                        . "time datetime,"
                        . "pass TEXT"
                        .");";
                        $stmt = $pdo->query($sql);
                        
                        foreach ($results as $row){
                            $id = $row['id'];
                            if ($id != $del){
                                $sql = $pdo -> prepare("INSERT INTO tbfinal (name, comment, time, pass) VALUES (:name, :comment, :time, :pass)");
                                $sql -> bindParam(':name', $name, PDO::PARAM_STR);  // bindParam ... 指定された変数名にパラメータをバインドする
                                $sql -> bindParam(':comment', $comment, PDO::PARAM_STR);
                                $sql -> bindParam(':time', $time, PDO::PARAM_STR);
                                $sql -> bindParam(':pass', $pass, PDO::PARAM_STR);
                                
                                $name = $row['name'];
                                $comment = $row['comment'];
                                $time = $row['time'];
                                $pass = $row['pass'];
                                $sql -> execute();
                            }
                        }
                    }
                }
            }
        }
            

        //編集選択
        if ($_SERVER['REQUEST_METHOD'] === 'POST'){
            if (isset($_POST['submit3'])){
                if (!empty($_POST["edit"]) && !empty($_POST["pass_edit"])){  
                    // データとパスワードの照合
                    $edit = $_POST["edit"];
                    $pass_edit = $_POST["pass_edit"];
                    
                    $sql = 'SELECT * FROM tbfinal where id=:edit';
                    $stmt = $pdo->prepare($sql);
                    $stmt->bindParam(':edit', $edit, PDO::PARAM_INT);
                    $stmt->execute();
                    $results = $stmt->fetch();
                    
                    $id = $results['id'];
                    $pass = $results['pass'];
                    
                    if ($id==$edit && $pass==$pass_edit){
                        $editnumber = $results['id'];
                        $editname = $results['name'];
                        $editcomment = $results['comment'];
                    }
                }
            }
        }
        
 
        //編集投稿
        if ($_SERVER['REQUEST_METHOD'] === 'POST'){
            if (isset($_POST['submit1'])){
                if (!empty($_POST["name"]) && !empty($_POST["comment"]) && !empty($_POST["edit_n"]) && !empty($_POST["pass_new"])){  //投稿の編集
                    $name = $_POST["name"];
                    $comment = $_POST["comment"];
                    $edit_n = $_POST["edit_n"];
                    $pass_new = $_POST["pass_new"];
                    $time = date("Y/m/d H:i:s");
                    
                    $sql = 'UPDATE tbfinal SET name=:name, comment=:comment, time=:time, pass=:pass WHERE id=:id';
                    $stmt = $pdo->prepare($sql);
                    $stmt->bindParam(':id', $edit_n, PDO::PARAM_STR);
                    $stmt->bindParam(':name', $name, PDO::PARAM_STR);
                    $stmt->bindParam(':comment', $comment, PDO::PARAM_STR);
                    $stmt->bindParam(':time', $time, PDO::PARAM_STR);
                    $stmt->bindParam(':pass', $pass_new, PDO::PARAM_STR);
                    $stmt->execute();
                }
            }
        }
                    
                    
    ?>
    
    <form  method="post">
        <p>投稿欄</p>
        <input type="text" name="name" placeholder="名前" value="<?php if(isset($editname)) {echo $editname;} ?>"><br>
        <input type="text" name="comment" placeholder="コメント" value="<?php if(isset($editcomment)) {echo $editcomment;} ?>"><br>
        <input type="password" name="pass_new" placeholder="パスワード"><br>
        <input type="hidden" name="edit_n" placeholder="パスワード" value="<?php if(isset($editnumber)) {echo $editnumber;} ?>">
        <input type="submit" name="submit1"><br><br>
        
        <!--削除フォーム-->
        <p>削除欄</p>
        <input type="number" placeholder="投稿番号" name="del"><br>
        <input type="password" placeholder="パスワード" name="pass_del"><br>
        <input type="submit" name="submit2"><br><br>
        
        <!--編集フォーム-->
        <p>編集欄</p>
        <input type="number" placeholder="投稿番号" name="edit"><br>
        <input type="password" placeholder="パスワード" name="pass_edit"><br>
        <input type="submit" name="submit3"><br>
    </form>
    
    <?php
         //新規投稿エラー
        if ($_SERVER['REQUEST_METHOD'] === 'POST'){ 
            if (isset($_POST['submit1'])){
                if (empty($_POST["name"])){
                    echo "!-----------------!<br>";
                    echo "Error: Name is Empty.<br>";
                    echo "!---------------!<br><br>";
                    
                }elseif (!empty($_POST["name"]) && empty($_POST["comment"])){
                    echo "!-----------------!<br>";
                    echo "Error: Comment is Empty.<br>";
                    echo "!-----------------!<br><br>";
                    
                }elseif (!empty($_POST["name"]) && !empty($_POST["comment"]) && empty($_POST["pass_new"])){
                    echo "!-----------------!<br>";
                    echo "Error: Password is Empty.<br>";
                    echo "!-----------------!<br><br>";
                }
            }
        }
        
        //削除エラー
        if ($_SERVER['REQUEST_METHOD'] === 'POST'){  
            if (isset($_POST['submit2'])){
                if (empty($_POST["del"])){
                    echo "!-----------------!<br>";
                    echo "Error: Delite-Number is Empty.<br>";
                    echo "!-----------------!<br><br>";
                    
                }elseif (!empty($_POST["del"]) && empty($_POST["pass_del"])){
                    echo "!-----------------!<br>";
                    echo "Error: Password is Empty.<br>";
                    echo "!-----------------!<br><br>";
                    
                }elseif (!empty($_POST["del"]) && !empty($_POST["pass_del"])){
                    $del = $_POST["del"];
                    $pass_del = $_POST["pass_del"];
                    
                    $sql = 'SELECT * FROM final where id=:del';
                    $stmt = $pdo->prepare($sql);
                    $stmt->bindParam(':del', $del, PDO::PARAM_INT);
                    $stmt->execute();
                    $results = $stmt->fetch();
                    
                    $id = $results['id'];
                    $pass = $results['pass'];
                    
                    if ($id==$del && $pass!=$pass_del){
                            echo "!-----------------!<br>";
                            echo "Error: Password is invalid.<br>";
                            echo "!-----------------!<br><br>";
                    }
                }
            }
        }
        
        //編集エラー
        if ($_SERVER['REQUEST_METHOD'] === 'POST'){  
            if (isset($_POST['submit3'])){
                if (empty($_POST["edit"])){
                    echo "!-----------------!<br>";
                    echo "Error: Edit-Number is Empty.<br>";
                    echo "!-----------------!<br><br>";
                    
                }elseif (!empty($_POST["edit"]) && empty($_POST["pass_edit"])){
                    echo "!-----------------!<br>";
                    echo "Error: Password is Empty.<br>";
                    echo "!-----------------!<br><br>";
                    
                }elseif (!empty($_POST["edit"]) && !empty($_POST["pass_edit"])){
                    $edit = $_POST["edit"];
                    $pass_edit = $_POST["pass_edit"];
                    
                    $sql = 'SELECT * FROM tb_5_1 where id=:edit';
                    $stmt = $pdo->prepare($sql);
                    $stmt->bindParam(':edit', $edit, PDO::PARAM_INT);
                    $stmt->execute();
                    $results = $stmt->fetch();
                    
                    $id = $results['id'];
                    $pass = $results['pass'];
                    
                    if ($id==$edit && $pass==$pass_edit){
                        echo "id".$edit."を編集します<br>";
                    }elseif ($id==$edit && $pass!=$pass_edit){
                        echo "!-----------------!<br>";
                        echo "Error: Password is invalid.<br>";
                        echo "!-----------------!<br><br>";
                    }
                }
            }
        }
        
        echo "<br>-----------------------------------------<br>";
        echo "【　データ一覧　】<br><br>";
        
        $sql = 'SELECT * FROM tbfinal';
        $stmt = $pdo->query($sql);
        $results = $stmt->fetchAll();
        foreach ($results as $row){
            echo $row['id'].'. ';
            echo $row['name'].' 「';
            echo $row['comment'].'」 ';
            echo $row['time'].'<br>';
            echo "<hr>";
        } 
    ?>
</body>
</html>