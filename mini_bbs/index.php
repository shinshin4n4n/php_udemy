<?php

  session_start();
  require('dbconnect.php');
  require('function.php');

  if(isset($_SESSION['id']) 
    && $_SESSION['time'] + 3600 > time()){ //一時間後に何もしていなければログアウトされる
    
      $_SESSION['time'] = time();

      $members = $db->prepare('SELECT * FROM members WHERE id=?');
      $members->execute(array($_SESSION['id']));
      $member = $members->fetch();

      //セッション切れでログイン画面に遷移
    }else{
      header('Location: login.php');
      exit();
    }

  if(!empty($_POST)){

      if($_POST['message'] !== ''){
        $message = $db->prepare('INSERT INTO posts SET member_id=?, message=?, reply_message_id=?, created=Now()');
        $message->execute(array(
          $member['id'],
          $_POST['message'],
          $_POST['reply_post_id']
        ));
    }

  header('Location: index.php');
  exit();
  }

$posts = $db->query('SELECT m.name, m.picture, p.* FROM members m, posts p WHERE m.id=p.member_id ORDER BY p.created DESC');

if(isset($_REQUEST['res'])){
  //返信の処理
  $response = $db->prepare('SELECT m.name, m.picture, p.* FROM members m, posts p WHERE m.id=p.member_id AND p.id=?');

  $response->execute(array($_REQUEST['res']));

  $table = $response->fetch();
  $message = '@'.$table['name'].' '.$table['message'];

}


?>



<!DOCTYPE html>
<html lang="ja">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="X-UA-Compatible" content="ie=edge">
	<title>ひとこと掲示板</title>
	<link rel="stylesheet" href="style.css" />
</head>

<body>
<div id="wrap">
  <div id="head">
    <h1>ひとこと掲示板</h1>
  </div>
  <div id="content">
  	<div style="text-align: right"><a href="logout.php">ログアウト</a></div>
    <form action="" method="post">
      <dl>
        <dt><?php echo h($member['name']); ?>さん、メッセージをどうぞ</dt>
        <dd>
          <textarea name="message" cols="50" rows="5"></textarea>
          <input type="hidden" name="reply_post_id" value="" />
        </dd>
      </dl>
      <div>
        <p>
          <input type="submit" value="投稿する" />
        </p>
      </div>
    </form>


    <?php foreach ($posts as $post): ?>
    <div class="msg">
    <img src="member_picture/<?php echo h($post['picture']); ?>" width="48" height="48" alt="" />
    <p>
        <?php echo h($post['message']); ?>
        <span class="name">（<?php echo h($post['name']); ?>）
        </span>
        [<a href="index.php?res=">Re</a>]
    </p>
    <p class="day"><a href="view.php?id="><?php echo h($post['created']); ?></a>
    <a href="view.php?id=">返信元のメッセージ</a>
[   <a href="delete.php?id=" style="color: #F33;">削除</a>]
    </p>
    </div>
    <?php endforeach;  ?>

<ul class="paging">
<li><a href="index.php?page=">前のページへ</a></li>
<li><a href="index.php?page=">次のページへ</a></li>
</ul>
  </div>
</div>
</body>
</html>
