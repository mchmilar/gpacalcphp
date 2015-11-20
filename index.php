<?PHP 

function processLogin() {
	
	$username = trim($_POST['username']);
	$password = trim($_POST['password']);
	
	$loginList = 'logins.txt';
	
	if(!validLoginFormat()) {
		$_POST = array();
		include("header.php");
		include("login.php");
		print("<p class=\"warning\">Bad Login</p>");
		return false;
	} elseif ( !file_exists($loginList) ) {
		$_POST = array();
		echo("error");
		return false;
	} else {
		$users = file($loginList);
		
		foreach ($users as $name) {
			$name = explode(" ", $name);
			//echo $name[0], $name[1], $username, $password;
			
			$sameName = $name[0] == $username;
			$samePW = trim($name[1]) == strtolower($password);
			//$sameName = !strcmp($name[0], $username);
			//$samePW = !strcmp($name[1], $password);
			//print($sameName);
			//print($samePW);
			if ( $sameName && $samePW) {
				$_SESSION['user'] = $username;
				return true;
			}
			
		}
		include("header.php");
		include("login.php");
		print("<p class=\"warning\">Username or Password incorrect</p>");
		return false;
		
	}
}

function validLoginFormat() {
	if (empty($_POST['username']) || empty($_POST['password'])) {
		return false;
	} else 
		return true;
}

if (empty($_POST['submit'])) {
	session_id(md5(time() . rand() . $_SERVER['REMOTE_ADDR']));
	session_start();
	include("header.php");
	include("login.php");
} elseif ($_POST['submit'] == "Submit") {
	session_start();
	
	if (processLogin()) {
		header("location: calculator.php");	
	}
}

?>
</div>
</body>
</html>