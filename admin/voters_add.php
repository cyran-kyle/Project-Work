<?php
	include 'includes/session.php';

	if(isset($_POST['add'])){
		$firstname = $_POST['firstname'];
		$lastname = $_POST['lastname'];
		$password = password_hash($_POST['password'], PASSWORD_DEFAULT);
		$filename = $_FILES['photo']['name'];

		if(!empty($filename)){
			move_uploaded_file($_FILES['photo']['tmp_name'], '../images/'.$filename);	
		}

		// Generate a unique voters ID in the format NLCXXXXXXXX
		$prefix = 'NLC';
		do {
			// Generate a random 8-digit number
			$number = str_pad(rand(0, 99999999), 8, '0', STR_PAD_LEFT); // Generates an 8-digit number
			$voter_id = $prefix . $number; // Combine prefix and number

			// Check if the voter ID already exists in the database
			$query = $conn->prepare("SELECT * FROM voters WHERE voters_id = ?");
			$query->bind_param('s', $voter_id);
			$query->execute();
			$result = $query->get_result();
		} while ($result->num_rows > 0); // Repeat if the ID already exists

		// Insert into the voters table
		$sql = "INSERT INTO voters (voters_id, password, firstname, lastname, photo) VALUES ('$voter_id', '$password', '$firstname', '$lastname', '$filename')";
		if($conn->query($sql)){
			$_SESSION['success'] = 'Voter added successfully';
		}
		else{
			$_SESSION['error'] = $conn->error;
		}
	}
	else{
		$_SESSION['error'] = 'Fill up add form first';
	}

	header('location: voters.php');
?>
