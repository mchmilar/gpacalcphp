<?PHP
session_start();

$fileName = $_SESSION['user'].".txt";
$username = ucfirst($_SESSION['user']);

if (empty($_POST['submit'])) {
	refreshContent();
	
} elseif ($_POST['submit'] == "Add" && !validCredits()) {
	refreshContent();
	print("<p class=\"warning\">Invalid Credits Input</p>");
} elseif ($_POST['submit'] == "Add" && !validGrade()) {
	refreshContent();
	print("<p class=\"warning\">Invalid Grade Input</p>");
} elseif ($_POST['submit'] == "Add" && !validName()) {
	refreshContent();
	print("<p class=\"warning\">Invalid Name Input</p>");
} elseif ($_POST['submit'] == "Add") {
	addToDatabase();
	refreshContent();
} elseif ($_POST['submit'] == "Remove Selected Courses") {
	removeFromDatabase();
	refreshContent();
} elseif ($_POST['submit'] == "Logout") {
	session_unset();
	session_destroy();
	header("location: index.php");
	
}

function validName() {
	$inputName = trim(strtoupper($_POST['name']));
	global $fileName;
	if (file_exists($fileName)) {
		$data = file($fileName);
		
		foreach ($data as $line) {
			$line = explode(",", $line);
			if (!strcmp($line[0], $inputName))
				return false;	
		}
	}
	return true;	
}

function validCredits() {
	if ($_POST['credits'] == NULL)
		return false;
	elseif ($_POST['credits'] < 0 || $_POST['credits'] > 6)
		return false;
	elseif (!is_numeric($_POST['credits']))
		return false;
	else return true;
		
}

function validGrade() { 
	$grade = trim($_POST['grade']);
	
	if (strlen($grade) > 2 || strlen($grade) == 0)
		return false;
	elseif ($grade == 'f' || $grade == 'F')
		return true;
	elseif (strlen($grade) == 1) 
		return preg_match("/[abcd]/i", $grade);
	else 
		return (preg_match("/[abcd][-+]/i", $grade) );
	
	
}

function refreshContent() {
	$fileName = $_SESSION['user'].".txt";
	$username = ucfirst($_SESSION['user']);
	include("header.php");
	include("calcForm.php");
	if (file_exists($fileName)) {
		$courses = file($fileName);
		foreach ($courses as $course) {
			$course = explode(",", $course);
			print("<tr>
			<td><input type=\"checkbox\" name=\"selectcourse[]\" value=\"$course[0]\"></td>
			<td>$course[0]</td>
			<td>$course[1]</td>
			<td>$course[2]</td></tr>");			
		}
		
		$gpa = calculateGPA();
		print("<tr><td></td></tr>");
		print("<tr><td colspan=\"4\">$username,  your GPA is: $gpa </td></tr>");
		print("<tr><td colspan=\"4\"><input type=\"submit\" name=\"submit\" id=\"remove\" value=\"Remove Selected Courses\"></td></tr>");
		print("<tr><td colspan=\"4\"><input type=\"submit\" name=\"submit\" id=\"logout\" value=\"Logout\"></td></tr>");			
	}	
}

function removeFromDatabase() {
	global $fileName;
	$data = file($fileName);
	$temp = fopen("temp.txt", "w");
	$coursesToRemove = $_POST['selectcourse'];
	$shouldWrite = true;
	
	foreach ($data as $line) {
		$line = explode(",", $line);
		
		foreach ($coursesToRemove as $courseName) {
			if ($courseName == $line[0])
				$shouldWrite = false;
		}
		
		if ($shouldWrite) {
			fwrite($temp, $line[0] . "," . $line[1] . "," .$line[2]);
				
		}
		
	}
	
	unlink($fileName);
	if (filesize("temp.txt") != 0) {
		rename("temp.txt", $fileName);
		chmod($fileName, 0700);
	} else
		unlink("temp.txt");
}

function addToDatabase() {
	global $fileName;
	$data = fopen($fileName, "a");
	fwrite($data, trim(strtoupper($_POST['name'])) . "," . trim($_POST['credits']) . "," . trim(strtoupper($_POST['grade'])) .PHP_EOL);
	fclose($data);
	chmod($fileName, 0700);
}

function calculateGPA() {
	global $fileName;
	$totalCredits = 0;
	$weightedGradePoints = 0;
	if (file_exists($fileName)) {
		$courses = file($fileName);
		foreach ($courses as $course) {
			$course = explode(",", $course);
			$totalCredits += $course[1];
			$weightedGradePoints += convertGrade($course[2]) * $course[1];		
		}
		return $weightedGradePoints / $totalCredits;	
	}
}

function convertGrade($letterGrade) {
	$grade = trim(strtolower($letterGrade));
	switch($grade) {
		case "a+": $grade = 4.3; break;
		case "a": $grade = 4.0; break;
		case "a-": $grade = 3.7; break;
		case "b+": $grade = 3.3; break;
		case "b": $grade = 3.0; break;
		case "b-": $grade = 2.7; break;
		case "c+": $grade = 2.3; break;
		case "c": $grade = 2.0; break;
		case "c-": $grade = 1.7; break;
		case "d+": $grade = 1.3; break;
		case "d": $grade = 1.0; break;
		case "d-": $grade = 0.7; break;
		case "f": $grade = 0.0; break;
	}
	return $grade;
}

?>

</table>


</form>
</div>
</body>
</html>