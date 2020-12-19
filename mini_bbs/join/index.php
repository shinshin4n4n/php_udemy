<?php
	//phpinfo();
	function h($s){
		return htmlspecialchars($s, ENT_QUOTES, 'UTF-8');
	}
	session_start();
	require('../dbconnect.php');

	if(!empty($_POST)){

		if($_POST['name'] === ''){
			$error['name'] = 'blank';
		}
		if($_POST['email'] === ''){
			$error['email'] = 'blank';
		}

		if(strlen($_POST['password']) < 4 ){
			$error['password'] = 'length';
		}

		if($_POST['password'] === ''){
			$error['password'] = 'blank';
		}

		$fileName = $_FILES['image']['name'];

		if(!empty($fileName)){
			$ext = substr($fileName, -3);

			if($ext !== 'jpg' && $ext !== 'png'){
				$error['image'] = 'type';
			}
		}

		//　アカウントの重複をチェック
		//入力状態が正しいのを確認してからDB通信を行う
		if(empty($error)){
			$member = $db->prepare('SELECT COUNT(*) AS cnt FROM members WHERE email=?');

			//SQLの実行
			$member->execute(array($_POST['email']));

			//レコードの確認
			$record = $member->fetch();

			//レコード数>0で重複エラー
			if($record['cnt'] > 0){
				$error['email'] = 'duplicate';
			} 
		}


		if(empty($error)){
		
		$image = date('YmdHis').$_FILES['image']['name'];
		move_uploaded_file($_FILES['image']['tmp_name'],
		'../member_picture/' . $image);
		
		$_SESSION['join'] = $_POST;
		$_SESSION['join']['image'] = $image;
		header('Location: check.php');
		exit();
		}
	}

	if($_REQUEST['action'] == 'rewrite' && isset($_SESSION['join'])){
		$_POST = $_SESSION['join'];
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
<p>次のフォームに必要事項をご記入ください。</p>
<form action="" method="post" enctype="multipart/form-data">
	<dl>
		<dt>ニックネーム<span class="required">必須</span></dt>
		<dd>
        	<input type="text" name="name" size="35" maxlength="255"
			 value="<?php 
			 	print(h($_POST['name']));
			 ?>" />
			<?php if($error['name'] === 'blank'){  ?>
			<p class="error">*ニックネームを入力してください</p>
			<?php }?>
		</dd>
		<dt>メールアドレス<span class="required">必須</span></dt>
		<dd>
        	<input type="text" name="email" size="35" maxlength="255" 
			value="<?php 
			 	print(h($_POST['email']));
			 ?>" />
			<?php if($error['email'] === 'blank'){  ?>
			<p class="error">*メールアドレスを入力してください</p>
			<?php }?>
			<?php if($error['email'] === 'duplicate'){  ?>
			<p class="error">*指定されたメールアドレスはすでに登録されています。</p>
			<?php }?>
		<dt>パスワード<span class="required">必須</span></dt>
		<dd>
        	<input type="password" name="password" size="10" maxlength="20" 
			value="<?php 
			 	print(h($_POST['password']));
			 ?>" />
			<?php if($error['password'] === 'blank'){  ?>
			<p class="error">*パスワードを入力してください</p>
			<?php }?>
			<?php if($error['password'] === 'length'){  ?>
			<p class="error">*パスワードは４文字以上で入力してください</p>
			<?php }?>
        </dd>
		<dt>写真など</dt>
		<dd>
        	<input type="file" name="image" size="35" value="test"  />
			<?php if($error['image'] === 'type'){  ?>
			<p class="error">*写真などは「.jpg」または「.png」のファイルを指定してください</p>
			<?php }?>
			<?php if(!empty($error)){  ?>
			<p class="error">*恐れ入りますが、もう一度選択してください。</p>
			<?php }?>
        </dd>
	</dl>
	<div><input type="submit" value="入力内容を確認する" /></div>
</form>
</div>
</body>
</html>
