<!--	Author: 	Noah Taylor
		Date:		Dec 4, 2022
		File:		Final Exam
-->

<html>
<head>
	<title>View Customers</title>
	
</head>
<body>
	<?php
		//sha1 len = 40
		//sha224 len = 56
		//sha256 len = 64
		
		/* TODO: */
		
		$lookup = $_POST["lookup"];
		$server = "localhost";
		$user = "root";
		$pw = null;
		$db = "passwords";
		$table = null;
		
		if(strlen($lookup) == 40)
		{
			$table = "sha1";
			print("<p>Searching for $lookup</p>");
		}
		elseif(strlen($lookup) == 56)
		{
			$table = "sha224";
			print("<p>Searching for $lookup</p>");
			
		}
		elseif(strlen($lookup) == 64)
		{
			$table = "sha256";
			print("<p>Searching for $lookup</p>");
		}
		else
		{
			print("Invalid sha length!");
		}
		
		
		if($table != null)
		{
			mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
			try
			{
				$mysqli = new mysqli($server, $user, $pw, $db);
			}
			catch(Exception $e)
			{
				error_log($e->getMessage());
				exit('Error connecting to database');
			}
			
			try
			{
				$stmt = $mysqli ->prepare("SELECT password FROM passwords.$table WHERE hash = '$lookup'"); 
				$stmt->execute();
				$result = $stmt->get_result();
				$num_rows = mysqli_num_rows($result);
			}
			catch(Exception $e)
			{
				print($e);
			}
			if($num_rows != 0)
			{
				print("<p>Match Found!</p>");
				while($row = $result->fetch_assoc())
				{
					print("<p>Password: " . $row["password"]."</p>");
				}
			}
			else
			{
				print("<p>No passwords match</p>");
			}
			$mysqli->close();
		}
	?>
	<h1>Please input the hash value to look up:</h1>
	<form action = "sha_lookup.php" method = "post">
		<p> SHA Value:
			<input type = "text" size = "100" name = "lookup">
		</p>
		<input type = "submit" value = "Search Again">
		<input type = "reset" value = "Clear">
</body>
</html>
