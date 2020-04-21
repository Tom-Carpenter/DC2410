<!DOCTYPE html>
<html>
<head>
  <title>Unit 3 Basic PHP Programing - Tasks </title>
</head>

<body>
	<h1>Unit 3 tasks</h1>

	<!-- Task 1: String-->
	<!-- write your solution to Task 1 here -->
	<?php   
		$string_var = "I love programming";

	?>
	<div class="section">
		<h2>Task 1 : String</h2>
		<p>
		<?php 
		print nl2br("First letter is: " . $string_var[0] . "\n");
		print nl2br("Length of string is: " . strlen($string_var) . "\n" );
		print nl2br("Last letter is:" . $string_var[strlen($string_var)-1] . "\n"); /* as zero indexed array */
		print nl2br("First 6 letters are" . substr($string_var,0,6) . "\n");
		print nl2br("In capital:" . strtoupper($string_var) . "\n");
		?>
		</p>
	
	</div>

	<!-- Task 2: Array and image-->
	<!-- write your solution to Task 2 here -->
	<div class="section">
		<h2>Task 2 : Array and image</h2>
		<?php
		$pic_array = array("earth.jpg","flower.jpg","plane.jpg","tiger.jpg");
		$rand_number = rand()%4;
		$image_dir = "images/" . $pic_array[$rand_number];
		?>
		<img src="<?php echo $image_dir ?>" alt="<?php echo $pic_array[$rand_number] ?>" width="20%"/>

		
	</div>	

	<!-- Task 3: Function definition dayinmonth  -->
	<!-- write your solution to Task 3 here -->
	<div class="section">
		<h2>Task 3 : Function definition</h2>
		<?php 
		function daysInMonth($month){
			$months_30_days = array(4,6,9,11);
		$months_31_days = array(1,3,5,7,8,10,12);
			if(in_array($month,$months_30_days)){
				return 30;
			}if(in_array($month,$months_31_days)){
				return 31;
			}elseif($month =2){
				return 28;
			}
			else {
				return -1;
			}
		}
		
		?>
		<ul>
		<li><?php echo daysInMonth(1) ?></li>
		<li><?php echo daysInMonth(2) ?></li>
		<li><?php echo daysInMonth(3) ?></li>
		<li><?php echo daysInMonth(4) ?></li>
		<li><?php echo daysInMonth(5) ?></li>
		<li><?php echo daysInMonth(6) ?></li>
		<li><?php echo daysInMonth(7) ?></li>
		<li><?php echo daysInMonth(8) ?></li>
		<li><?php echo daysInMonth(9) ?></li>
		<li><?php echo daysInMonth(10) ?></li>
		<li><?php echo daysInMonth(11) ?></li>
		<li><?php echo daysInMonth(12) ?></li>
		</ul>
	</div>
	

	
	<!-- Task 4: Favorite Artists from a File (Files) -->
	<!-- write your solution to Task 4 here -->
	<div class="section">
		<h2>Task 4: My Favorite Artists from a file</h2>
		<?php 
			$artists = file("favorite.txt");
			function formatArtistUrl($artist){
				$name_parts = explode(" ", $artist);
				return "http://www.mtv.com/artists/" . $name_parts[0] . "-" . $name_parts[1] . "/";
			}
			echo "<ol>";
			foreach($artists as $artist){
				$url = formatArtistUrl($artist);
				echo "<li><a href=$url>$artist</a></li>";
			}
			echo "</ol>";
		?>
		
		
	</div>
	
	<!-- Task 6: Directory operations -->
	<!-- write your solution to Task 6 here -->
	<div class="section">
		<h2>Task 6 : Directory operations</h2>
		<?php 
			$directory = scandir(".");
			echo "<ul>";
			foreach($directory as $to_check){
				if(!is_dir($to_check) && $to_check[0]!='.'){
					echo "<li>$to_check</li>";
				}
			}
			echo "</ul>";


?>

		
	</div>

	<!-- Task 6 optional: Directory operations -->
	<!-- write your solution to Task 6 optional here -->
	<div class="section">
		<h2>Task 6 optional: Directory operations optional</h2>
		<?php 
			function getDirContents($path) {
				$rii = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path));
			
				$files = array(); 
				foreach ($rii as $file)
					if (!$file->isDir())
						$files[] = $file->getPathname();
			
				return $files;
			}
			
			$files = getDirContents("."); //current directory
			foreach($files as $file){
				echo "<p>$file</p>";
			}
?>
	
	
	</div>
	</div


	
    <!-- Task 5: including external files -->
	<!-- write your solution to Task 5 here -->
	<div class="section">
		<h2>Task 5: including external files</h2>
		<?php include 'footer.php' ?>
			
	</div>

</body>
</html>
