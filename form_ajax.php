<?php
if(isset($_POST['name'],$_POST['phone'],$_POST['email'],$_POST['password']))
{
	$errorsArray = [];
	if(empty($_POST['name']))
	{
		$errorsArray[] = 'Name is required';
	}

	if(empty($_POST['phone']))
	{
		$errorsArray[] = 'Phone is required';
	}

	if(empty($_POST['email']))
	{
		$errorsArray[] = 'Email is required';
	}

	if(empty($_POST['password']))
	{
		$errorsArray[] = 'Password is required';
	}

	if(!filter_var(trim($_POST['email']),FILTER_VALIDATE_EMAIL))
	{
		$errorsArray[]="Invalid email format";
	}

	if(count($errorsArray) > 0)
	{
		$errors['error_count'] = count($errorsArray);
		$errors['error_msg'] = $errorsArray;
		echo json_encode($errors);
		exit();
	}

	$servername = "phpvalidregform";
	$username ="root";
	$password = "";
	$database = 'justform';

	try {
		$conn = new PDO("mysql:host=$servername;dbname=".$database, $username, $password);
		$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	} catch (PDOException $e) {

		echo json_encode(['error_msg' => $e->getMessage(),'error_count'=> 1]);
		exit();
	}


	$name = htmlspecialchars(trim($_POST['name']));
	$email = htmlspecialchars(trim($_POST['email']));
	$phone = htmlspecialchars(trim($_POST['phone']));
	$password = trim($_POST['password']);

	$hashPassword = password_hash($password,PASSWORD_BCRYPT,['cost'=>12]);

	$sql = "INSERT INTO users (name, email, password, phone, joining_date) VALUES(:name,:email,:password,:phone,:joining_date)";

	$prepare = $conn->prepare($sql);
	$params = [
		'name' => $name,
		'email'=> $email,
		'password'=> $hashPassword,
		'phone' => $phone,
		'joining_date'=> date('Y-m-d H:i:s')
];

	try{
		$prepare->execute($params);
		echo json_encode(['success_msg' => 'User has been registered']);
		exit();
	}
	catch (Exception $e) {
		echo json_encode(['error_msg' => $e->getMessage(),'error_count' => 1]);
		exit();
	}
}

echo json_encode(['error_msg' => 'Access Denied', 'error_count' =>1]);
exit();

?>;
