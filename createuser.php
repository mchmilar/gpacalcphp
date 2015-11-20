<?PHP



if (empty($_POST['submit'])) {
	include("header.php");
	include("createuserform.php");

} elseif ($_POST['submit'] == "Submit") {
	$username = trim(strtolower($_POST['username']));
	$password1 = $_POST['password'];
	$password2 = $_POST['password2'];
	if (!validUserName()) {
		include("header.php");
		include("createuserform.php");
		print("<p class=\"warning\">invalid username, or it's already taken</p>");
	} elseif (!validPassword()) {
		include("header.php");
		include("createuserform.php");
		print("<p class=\"warning\">invalid password or passwords do not match</p>");
	} else {
		createUser();
	}
		
}

function createUser() {
	$userlist = fopen("logins.txt", "a");
	
	
	fwrite($userlist, trim(strtolower($_POST['username'])) . " " . trim($_POST['password']) .PHP_EOL);
	fclose($userlist);	
	echo("<p>Account created! <a href=\"index.php\">Click here to login</a></p>");
}

function validPassword() {
	if ( !preg_match('/^[A-Za-z][A-Za-z0-9]{5,31}$/', $_POST['password']) )
		return false;
	
	if ( $_POST['password'] != $_POST['password2'])
		return false;
		
	return true;	
}


function validUserName() {
	$data = file("logins.txt");
	
	foreach ($data as $line) {
		$line = explode(" ", $line);
		if (trim(strtolower($line[0])) == trim(strtolower($_POST['username'])))
			return false;	
	}
	
	if ( !preg_match('/^[A-Za-z][A-Za-z0-9]{5,31}$/', $_POST['username']) )
		return false;
		
	return true;
}
?>