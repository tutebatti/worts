<?php
	// ini_set('display_errors', 1);
	// ini_set('display_startup_errors', 1);
	// error_reporting(E_ALL);

	include('./config/db_connect.php');
	include('./config/classes.php');

	$title_field = $note_field = '';
	$title_error = '';

	if(isset($_POST['add_lesson']))
	{
		$title_field = $_POST['title_field'];
		$note_field = $_POST['note_field'];

		if(empty($title_field))
		{
			$title_error = 'Title can’t be empty.';
		}
		else
		{
			try
			{
				$sql = 'INSERT INTO lessons(title, note) VALUES(?, ?)';
				$values = [$title_field, $note_field];
				pdo($pdo, $sql, $values);

				$lesson_id = $pdo->lastInsertId();

				header('Location: lesson_details.php?id=' . $lesson_id);
			}
			catch(PDOException $e)
			{
				exit('Unable to add to database.');
			}
		}
	}
?>

<!DOCTYPE html>
<html>
	<?php include('templates/header.php'); ?>

	<section class="container grey-text">
		
		<h4 class="center">Add a lesson</h4>
		
		<form class="white adding" action="add_lesson.php" method="POST">
			<div class="row">
				<div class="col s12">
					<input type="text" name="title_field" autofocus="autofocus" value="<?php echo $title_field; ?>"> <!-- encode output? -->
					<label>Title</label>
					<div class="red-text"><?php echo $title_error; ?></div>
				</div>
				<div class="col s12">
					<input type="text" name="note_field" value="<?php echo $note_field; ?>"> <!-- encode output? -->
					<label>Note</label>
				</div>
				<div class="col s4 offset-s2">
					<input type="submit" name="add_lesson" value="Add lesson" class="btn base-style inline-button z-depth-0">
				</div>
				<div class="col s4">
					<a href="index.php">
						<div class="btn grey inline-button z-depth-0">Cancel</div>
					</a>
				</div>
			</div>
		</form>

	</section>

	<?php include('templates/footer.php'); ?>
</html>
