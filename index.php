<?php
	// ini_set('display_errors', 1);
	// ini_set('display_startup_errors', 1);
	// error_reporting(E_ALL);

	include('./config/db_connect.php');
	include('./config/classes.php');

	$lesson_ids = pdo($pdo, 'SELECT id FROM lessons')->fetchAll(PDO::FETCH_COLUMN, 0);
?>

<!DOCTYPE html>
<html>
	<?php include('templates/header.php'); ?>

	<div class="container center">
		
		<h4 class="grey-text">Lessons</h4>
		
		<div class="row">

			<div class="col s12 m6 l4">
				<a href="add_lesson.php" style="display:block;" class="">
					<div class="card tiny center base-style z-depth-0 white-text">
						<div class="card-content">
							<h5 style="padding-top:1.5em;">+ Add a lesson</h5>
						</div>
					</div>
				</a>
			</div>

			<?php foreach($lesson_ids as $lesson_id){ ?>

				<div class="col s12 m6 l4">
					<div class="card tiny z-depth-0">
						<div class="btn-floating grey level">
                                <?php
									$lesson = new Lesson($pdo, $lesson_id);
									$sql = 'SELECT count(*) FROM flashcards WHERE lesson = ?';
									echo pdo($pdo, $sql, [$lesson->id])->fetchColumn();
								?>
                        </div>
						<div class="card-content">
							<h5>
								<?php echo $lesson->title; ?>
							</h5>
							<p class="grey-text">
								<?php echo $lesson->note; ?>
							</p>
						</div>
						<div class="card-action right-align">
							<a class="base-text-style" href="lesson_details.php?id=<?php echo $lesson->id ?>">Go to lesson</a>
						</div>
					</div>
				</div>

			<?php } ?>

		</div>
	</div>
	
	<?php include('templates/footer.php'); ?>
</html>
