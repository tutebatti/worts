<?php
	// ini_set('display_errors', 1);
	// ini_set('display_startup_errors', 1);
	// error_reporting(E_ALL);

	include('./config/db_connect.php');
	include('./config/classes.php');

	$title_error = '';

	if(isset($_GET['id']))
	{
		$lesson = new Lesson($pdo, $_GET['id']);
		$title_field = $lesson->title;
		$note_field = $lesson->note;
	}

	if(isset($_POST['edit_lesson']))
	{
		$title_field = $_POST['title_field'];
		$note_field = $_POST['note_field'];

		if(empty($title_field))
		{
			$title_field = $lesson->title;
			$title_error = 'Title can’t be empty.';
		}
		else
		{
			try
			{
				$lesson->update('title', $title_field);
				$lesson->update('note', $note_field);
			}
			catch(PDOException $e)
			{
				exit('Unable to update database.');
			}

			header('Location: lesson_details.php?id=' . $lesson->id);
		}
	}
?>

<!DOCTYPE html>
<html>
	<?php include('templates/header.php'); ?>

	<?php if($lesson): ?>
		
		<section class="container grey-text">
			
			<h4 class="center">Edit lesson</h4>
			
			<form class="white adding" action="edit_lesson.php?id=<?php echo $lesson->id; ?>" method="POST">
				<div class="row">
					<div class="col s12">
						<input type="text" name="title_field" value="<?php echo $title_field; ?>">
						<label>Title</label>
						<div class="red-text"><?php echo $title_error; ?></div>
					</div>
					<div class="col s12">
						<input type="text" name="note_field" value="<?php echo $note_field; ?>">
						<label>Note</label>
					</div>
					<div class="col s4 offset-s2">
						<input type="submit" name="edit_lesson" value="Submit" class="btn base-style inline-button z-depth-0">
					</div>
					<div class="col s4">
					<a href="lesson_details.php?id=<?php echo $lesson_id; ?>">
						<div class="btn grey inline-button z-depth-0">Cancel</div>
					</a>
				</div>
				</div>
			</form>
		</section>
	<?php else: ?>
        <h4 class="red-text center">No such lesson exists.</h5>
    <?php endif ?>

	<?php include('templates/footer.php'); ?>
</html>
