<!DOCTYPE html>
<!--	Author: Noah Taylor
		Date:	Dec 4, 2022
		File:	Final Exam
		
-->


<html>
<head>
	<title>createDB</title>
</head>
<body>
	<?php
		
		//returns a 2D array from a file that has the passwords then the hash value separated by a colon on each line
		//EX: 123456:7c4a8d09ca3762af61e59520943dc26494f8941b
		//0 index in each row is password, then hash. Each one has ' on either side of the entry
		function get_array_data($fileName)
		{
			$file = fopen($fileName, "r");
			$i = 0;
			while($line = (String) fgets($file))
			{
				$temp[$i] = $line;
				$i++;
			}
			fclose($file);
			$temp = str_replace(array("\n", "\r"), '', $temp);
			$values = [];
			for($k = 0; $k < sizeof($temp); $k++)
			{
				$a = explode(":", $temp[$k]);
				$values[$k][0] = (String) "'".$a[0]."'";
				$values[$k][1] = (String) "'".$a[1]."'";
			}
			return $values;
		}
		
		/* inserts new columns into the three tables, then fills them in based on their respective info */
		function make_table($database, $tableName, $values, $con)
		{
			$userQuery = "CREATE TABLE $tableName(password varchar(100) NULL, hash varchar(100) NULL);";
			$result = mysqli_query($con, $userQuery);
			if (!$result) 
			{
				die("Could not successfully insert columns ($userQuery) " .mysqli_error($con));
			}
			
			$part1 = "INSERT INTO $tableName (password, hash) VALUES ";
			$part2 = null;
			for($i = 0; $i < sizeof($values); $i++)
			{
				$pass = $values[$i][0];
				$hash = $values[$i][1];
				$part2.= "(".$pass.", ". $hash.")";
				if($i < sizeof($values) - 1)
				{
					$part2.= ", ";
				}
				else
				{
					$part2.= ";";
				}
			} 
			$userQuery2 = $part1 . $part2;
			/*print("<h1>Insert</h1>");
			print("<pre>");
			print_r($userQuery2);
			print("</pre>");*/
			$result = mysqli_query($con, $userQuery2);
			if (!$result) 
			{
				die("Could not successfully insert rows in $tableName ($userQuery2) " .mysqli_error($con));
			}
		}
		
		$sha1 = get_array_data("sha1_list.txt");
		/*print("<h1>Sha 1</h1>");
		print("<pre>");
		print_r($sha1);
		print("</pre>");*/
	
		$sha224 = get_array_data("sha224_list.txt");
		//print("<h1>Sha 224</h1>");
		//print("<pre>");
		//print_r($sha224);
		//print("</pre>");
		
		$sha256 = get_array_data("sha256_list.txt");
		//print("<h1>Sha 256</h1>");
		//print("<pre>");
		//print_r($sha256);
		//print("</pre>");
		
		
		$server = "localhost";
		$user = "root";
		$pw = null; 
		
		$connect = mysqli_connect($server, $user, $pw);
		if(!$connect) 
		{
			print("Cannot connect");
			die();
		}
		
		$userQuery = "DROP DATABASE IF EXISTS passwords;";
		$userQuery2 = "CREATE DATABASE passwords;";
		$userQuery3 = "USE passwords;";
		$result = mysqli_query($connect, $userQuery);
		$result2 = mysqli_query($connect, $userQuery2);
		$result3 = mysqli_query($connect, $userQuery3);
		if (!$result) 
		{
			die("Could not successfully run query ($userQuery) " .mysqli_error($connect));
		}
		elseif(!$result2)
		{
			die("Could not successfully run query ($userQuery2) " .mysqli_error($connect));
		}
		elseif(!$result3)
		{
			die("Could not successfully run query ($userQuery3) " .mysqli_error($connect));
		}
		
		make_table("passwords", "sha1", $sha1, $connect); 
		make_table("passwords", "sha224", $sha224, $connect);
		make_table("passwords", "sha256", $sha256, $connect);
		
		mysqli_close($connect);	   
	?>	
</body>
</html>
