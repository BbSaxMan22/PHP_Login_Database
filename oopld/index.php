<?php

require_once 'core/init.php';

// echo Config::get('mysql/host'); //output 127.0.0.1


// DB::getInstance();


// echo Session::flash('success');//maybe add markup around it 


/*

if (Session::exists('home')) {
	echo '<p>' . Session::flash('home') . '</p>';
}

*/


// echo Session::get(Config::get(session/session_name));


// $user = new User(); // current user
// $otherUser = new User(6); // this will get ANOTHER user at value(id) 6 : create a user object from it
// echo $user->data()->username;
/*
if ($user->isLoggedIn()) {
	// echo 'Logged In';
?>
	<p>Hello <a href="#"><?php echo escape($user->data()->username); ?></a>!</p>

	<ul>
		<li><a href="logout.php">Log Out</></li>
	</ul>
<?php
} else {
	echo '<p>You need to <a href="login.php">log in</a> or <a href="register.php">register</a></p>';
}

*/

if(Session::exists('home')) {
	echo '<p>' . Session::flash('home') . '</p>';
}

$user = new User();
if ($user->isLoggedIn()) {
?>
	<p>Hello <a href="profile.php?user=<?php echo escape($user->data()->username); ?>"><?php echo escape($user->data()->username); ?></a></p>

	<ul>
		<li><a href="logout.php">Log Out</a></li>
		<!--<li><a href="update.php">Update Details</a></li>-->
		<li><a href="changepassword.php">Change Password</a></li>
	</ul>

<?php

	if ($user->hasPermission('admin')) {
		echo '<p>You are an administrator</p>';
	}

} else {
	echo '<p>You need to <a href="login.php">log in</a> or <a href="register.php">register</p>';
}