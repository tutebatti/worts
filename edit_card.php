<?php
	// ini_set('display_errors', 1);
	// ini_set('display_startup_errors', 1);
	// error_reporting(E_ALL);

	include('./config/db_connect.php');
	include('./config/classes.php');

	$front_field = $back_field = $note_field = '';
	$errors = array('front_error'=>'', 'back_error'=>'');

	if(isset($_GET['id']))
	{
		$card = new Flashcard($pdo, $_GET['id']);

		$front_field = $card->front;
		$back_field = $card->back;
		$note_field = $card->note;

		$lesson = new Lesson($pdo, $card->lesson);
		
		$lesson->title;
	}
	
	if(isset($_POST['edit_card']))
	{
		$front_field = $_POST['front_field'];
		$back_field = $_POST['back_field'];
		$note_field = $_POST['note_field'];

		if(empty($front_field))
		{
			$errors['front_error'] = 'Front side can’t be empty.';
			$front_field = $card->front;
		}
		
		if(empty($back_field))
		{
			$errors['back_error'] = 'Back side can’t be empty.';
			$back_field = $card->back;
		}
		
		if(!array_filter($errors))
		{
			try{
				$card->update('front', $front_field);
				$card->update('back', $back_field);
				$card->update('note', $note_field);
			}
			catch(PDOException $e)
			{
				exit('Unable to edit database.');
			}

			header('Location: card_details.php?id=' . $card->id);
		}
	}
?>

<!DOCTYPE html>
<html>
	<?php include('templates/header.php'); ?>

	<section class="container grey-text">
		<?php if($card): ?>
			
			<h6 class="center"><?php echo $lesson->title; ?></h6>
			
			<h4 class="center">Edit card</h4>
			
			<form class="white adding" action="edit_card.php?id=<?php echo $card->id; ?>" method="POST">
				<div class="row">
					<div class="col s12">
						<input type="text" name="front_field" value="<?php echo $front_field; ?>">
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
						<input type="submit" name="edit_card" value="Submit" class="btn base-style inline-button z-depth-0">
					</div>
					<div class="col s4">
						<a href="card_details.php?id=<?php echo $card->id; ?>">
							<div class="btn grey inline-button z-depth-0">Cancel</div>
						</a>
					</div>
				</div>
				</div>
			</form>
		<?php else: ?>
			<h4 class="center red-text">No such card exists.</h4>
		<?php endif ?>
	</section>

	<?php include('templates/footer.php'); ?>
</html>
