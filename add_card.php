<?php
	// ini_set('display_errors', 1);
	// ini_set('display_startup_errors', 1);
	// error_reporting(E_ALL);

	include('./config/db_connect.php');
	include('./config/classes.php');
	
	$front_field = $back_field = $note_field = '';
	$errors = array('front_error'=>'', 'back_error'=>'');

	if(isset($_GET['lesson_id']))
	{
		$lesson = new Lesson($pdo, $_GET['lesson_id']);
	}
	
	if(isset($_POST['add_card']))
	{
		$front_field = $_POST['front_field'];
		$back_field = $_POST['back_field'];
		$note_field = $_POST['note_field'];

		if(empty($front_field))
		{
			$errors['front_error'] = 'Front side can’t be empty.';
		}

		if(empty($back_field))
		{
			$errors['back_error'] = 'Back side can’t be empty.';
		}
		
		if(!array_filter($errors))
		{
			try{
				$sql = 'INSERT INTO flashcards(lesson, front, back, note) VALUES(?, ?, ?, ?)';
				$values = [$lesson->id, $front_field, $back_field, $note_field];
				pdo($pdo, $sql, $values);

			}
			catch(PDOException $e)
			{
				exit('Unable to add to database.');
			}

			header('Location: add_card.php?lesson_id=' . $lesson->id);
		}
	}
?>

<!DOCTYPE html>
<html>
	<?php include('templates/header.php'); ?>

	<section class="container grey-text">
		<?php if($_GET['lesson_id']): ?>
		
			<h6 class="center"><?php echo $lesson->title; ?></h6>
		
			<h4 class="center">Add a card</h4>
		
			<form class="white adding" action="add_card.php?lesson_id=<?php echo $lesson->id; ?>" method="POST">
				<div class="row">
					<div class="col s12">
						<input type="text" name="front_field" autofocus="autofocus" value="<?php echo $front_field; ?>">
						<label>Front</label>
						<div class="red-text"><?php echo $errors['front_error']; ?></div>
					</div>
					<div class="col s12">
						<input type="text" name="back_field" value="<?php echo $back_field; ?>">
						<label>Back</label>
						<div class="red-text"><?php echo $errors['back_error']; ?></div>
					</div>
					<div class="col s12">
						<input type="text" name="note_field" value="<?php echo $note_field; ?>">
						<label>Note</label>
					</div>
					<div class="col s4 offset-s2">
						<input type="submit" name="add_card" value="Add card" class="btn base-style inline-button z-depth-0">
					</div>
					<div class="col s4">
						<a href="lesson_details.php?id=<?php echo $lesson->id; ?>">
							<div class="btn grey inline-button z-depth-0">Cancel</div>
						</a>
					</div>
				</div>
			</form>
		<?php else: ?>
			<h4 class="center red-text">No lesson chosen.</h4>
		<?php endif ?>
	</section>
			
	<?php include('templates/footer.php'); ?>
</html>
