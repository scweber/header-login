<?php
	include 'authorizedGitUsers.php';
	$headers = apache_request_headers();
	$current_user = strtolower($headers['X-cn']);

//	if(in_array($current_user, $authorized_users)) {
		if(isset($_POST['revision'])) {
				pullRevision($_POST['revision'], $current_user);
		} else {
				echo "<form action='gitpull.php' method='POST'>";
						echo "<h4>How many revisions would you like to roll back (leave blank to perform git pull): </h4>";
						echo "<input type='text' size='10' name='revision' id='revision' value=''></input>";
						echo "<button type='submit'>Pull Revision</button>";
				echo "</form>";
		}
//	} else {
//		header('Location: https://' . $_SERVER['SERVER_NAME']);
//		exit;
//	}

	function pullRevision($revisions, $current_user) {
		$path = '/home/scweber/header-login/';
		chdir($path);
		if(isset($revisions)) {
				echo "<h4>Performing: 'git rebase -i -v HEAD~" . $revisions . " as user: " . $current_user . "</h4>";
				echo "<h5>" . shell_exec("git rebase -i -v HEAD~" . $revisions . " 2>&1") . "</h5>";
		} else {
				echo "<h4>Performing: 'git pull' as user: " . $current_user . "</h4>";
				echo "<h5>" . shell_exec("git pull -v 2>&1") . "</h5>";
		}
	}
?>
