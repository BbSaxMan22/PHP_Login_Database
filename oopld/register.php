<?php
require_once 'core/init.php';

// var_dump(Token::check(Input::get('token')));

if (Input::exists()) {
	if (Token::check(Input::get('token'))) {

		// echo 'this code ran', '<br>';

		$validate = new Validation();
		$validation = $validate->check($_POST, array(
			'username' => array(
				'required' => true,
				'min' => 2,
				'max' => 20,
				'unique' => 'users'
			),
			'password' => array(
				'required' => true,
				'min' => 6,
				//can include a strength meter
			),
			'password_again' => array(
				'required' => true,
				'matches' => 'password'
			),
			'name' => array(
				'required' => true,
				'min' => 2,
				'max' => 50
			)
		));

		if ($validation->passed()) {
			// echo 'Thanks';
			// Session::flash('success', 'You registered successfully');
			// header('Location: index.php');// take user to index after successful registration
			$user = new User();

			$salt = Hash::salt(32);

			try {

				$user->create(array(
					'username' => Input::get('username'),
					'password' => Hash::make(Input::get('password'), $salt),
					'salt' => $salt,
					'name' => Input::get('name'),
					'joined' => date('Y-m-d H:i:s'),
					'group' => 1
					));// if the salt is not stored in users spot in database their account will be made unrecoverable

				Session::flash('home', 'You have been registered and can now log in!');
				// header('Location: index.php'); // want to change the way user is redirected

				Redirect::to('index.php');

			} catch (Exception $e) {
				die($e->getMessage());
			}// this is good for debugging the code, but not user friendly
			// TODO redirect the user to a page when the user creation fails (404.php)
		} else {
			// print_r($validation->errors());
			foreach ($validation->error() as $error) {
				echo $error, '<br>';
			}
		}

	}

}

?>
<form action="" method="post">
	<div class="field">
		<label for="username">Username</label>
		<input type="text" name="username" id="username" value="<?php echo escape(Input::get('username')); ?>" autocomplete="off">

	</div>

	<div class="field">
		<label for="password">Choose a password</label>
		<input type="password" name="password" id="password">
	</div>

	<div class="field">
		<label for="password_again">Confirm your password</label>
		<input type="password" name="password_again" id="password_again">
	</div>

	<div class="field">
		<label for="name">Enter your name</label>
		<input type="text" name="name" value="<?php escape(Input::get('name')); ?>" id="name">
	</div>

	<input type="hidden" name="token" value="<?php echo Token::generate(); ?>">
	<input type="submit" value="register">

</form>

