<?php
	include 'authorizedGitUsers.php';
	$headers = apache_request_headers();
	$current_user = strtolower($headers['X-cn']);

	if(in_array($current_user, $authorized_users)) {
		if(isset($_POST['pull'])) {
			pullRevision($current_user);
		} else if (isset($_POST['pushChanges']) && $_POST['pushChanges'] != '') {
			pushRevision($_POST['pushChanges'], $current_user);
		} else if (isset($_POST['pushChanges']) && $_POST['pushChanges'] == '') {
			echo "You must enter a commit message...";
			echo "<form action='gitpull.php' method='POST'>";
				echo "<h4>Please enter a message for your commit: </h4>";
				echo "<input type='text' size='50' name='pushChanges' id='pushChanges' value=''></input>";
				echo "<button type='submit'>Push Changes</button><br/><br/>";
				echo "<button name='pull' type='submit'>Pull Changes</button>";
			echo "</form>";
		} else {
			echo "<form action='gitpull.php' method='POST'>";
				echo "<h4>Please enter a message for your commit: </h4>";
				echo "<input type='text' size='50' name='pushChanges' id='pushChanges' value=''></input>";
				echo "<button type='submit'>Push Changes</button><br/><br/>";
				echo "<button name='pull' type='submit'>Pull Changes</button>";
			echo "</form>";
		}
	} else {
		header('Location: https://' . $_SERVER['SERVER_NAME']);
		exit;
	}

	function pullRevision($current_user) {
		$path = '/home/scweber/header-login/';
		chdir($path);
		echo "<h4>Performing: 'git pull' as user: " . $current_user . "</h4>";
		echo "<h5>" . shell_exec("git pull -v 2>&1") . "</h5>";
		error_log('Performed git pull as user: ' . $current_user);
	}
	
	function pushRevision($message, $current_user) {
		$path = '/home/scweber/header-login/';
		chdir($path);
		echo "<h4>Performing: 'git push' as user: " . $current_user . "</h4>";
		echo "<h5>" . shell_exec("git add . -v 2>&1") . "</h5>";
		echo "<h5>" . shell_exec("git commit -m '" . $message . "' -v 2>&1") . "</h5>";
		echo "<h5>" . shell_exec("git push -v 2>&1") . "</h5>";
		error_log('Performed git pull as user: ' . $current_user);
	}
?>