<?php

	session_start();
	require('../dbconnect.php');
	require('../function.php');

	//print ($_SESSION['join']);

	if(!isset($_SESSION['join'])){
		header('Location: index.php');
		exit();
	}
	if(!empty($_POST)){
		$stmt = $db->prepare('INSERT INTO members SET name=?,
		email=?, password=?, picture=?, created=NOW()');

		//SQL実行
		$stmt->execute(array(
			$_SESSION['join']['name'],
			$_SESSION['join']['email'],
			sha1($_SESSION['join']['password']),
			$_SESSION['join']['image']
		));

		//セッションの削除
		unset($_SESSION['join']);

		//画面遷移
		header('Location: thanks.php');
		exit();
	}

?>

<!DOCTYPE html>
<html lang="ja">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="X-UA-Compatible" content="ie=edge">
	<title>会員登録</title>

	<link rel="stylesheet" href="../style.css" />
</head>
<body>
<div id="wrap">
<div id="head">
<h1>会員登録</h1>
</div>

<div id="content">
<p>記入した内容を確認して、「登録する」ボタンをクリックしてください</p>
<form action="" method="post">
	<input type="hidden" name="action" value="submit" />
	<dl>
		<dt>ニックネーム</dt>
		<dd>
		<?php print(h($_SESSION['join']['name'])); ?>
        </dd>
		<dt>メールアドレス</dt>
		<dd>
		<?php print(h($_SESSION['join']['email'])); ?>
        </dd>
		<dt>パスワード</dt>
		<dd>
		【表示されません】
		</dd>
		<dt>写真など</dt>
		<dd>
		<?php if($_SESSION['join']['image'] !== ''){ 
			//var_dump($_SESSION['join']['image']);
			?>
			<img src="../member_picture/<?php print h($_SESSION['join']['image']); ?>" alt="">
		<?php } ?>	
		</dd>
	</dl>
	<div><a href="index.php?action=rewrite">&laquo;&nbsp;書き直す</a> | <input type="submit" value="登録する" /></div>
</form>
</div>

</div>
</body>
</html>
