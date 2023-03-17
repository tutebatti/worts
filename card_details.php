<?php
    // ini_set('display_errors', 1);
    // ini_set('display_startup_errors', 1);
    // error_reporting(E_ALL);

	include('./config/db_connect.php');
	include('./config/classes.php');

    if(isset($_GET['id']))
    {

        $delete = 'n';

        $card = new Flashcard($pdo, $_GET['id']);
		$lesson = new Lesson($pdo, $card->lesson);

        if(isset($_POST['edit']))
        {
            header('Location: edit_card.php?id=' . $card->id);
        }

        if(isset($_POST['delete']))
        {
            $delete = 'y';
        }
        
        if(isset($_POST['confirm']))
        {
            // 2do: double check with alert
            pdo($pdo, 'DELETE FROM flashcards WHERE id = ?', [$card->id]);

            header('Location: lesson_details.php?id=' . $card->lesson);
        }
    }
?>
<!DOCTYPE html>
<html>
	<?php include('templates/header.php'); ?>
    
    <div class="container center">
        <?php if($card): ?>
            <div class="row">
                <div class="col s12 m8 offset-m2 l6 offset-l3">
                    <div class="card">
                        <div class="btn-floating base-style level level-<?php echo $card->level; ?>">
                            <?php echo $card->level; ?>
                        </div>
                        <div class="card-content">
                            <div class="row">
                                <div class="col s12">
                                    <h4>
                                        <?php echo $card->front; ?>
                                    </h4>
                                    <hr style="width:30%;"/>
                                    <h4>
                                        <?php echo $card->back; ?>
                                    </h4>
                                </div>
                                <div class="col s12">
                                    <h5 class="grey-text">
                                        <?php echo $card->note;?>
                                    </h5>
                                </div>
                                <div class="col s12">
                                    <h6>
                                        <b>Lesson:</b> <?php echo $lesson->title; ?>
                                    </h6>
                                </div>
                                <div class="col s12">
                                    <h6>
                                        <b>Created on:</b> <?php echo date("M d Y", strtotime($card->created_at)); ?>
                                    </h6>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col s4 offset-s2 m3 offset-m3 l2 offset-l4">
                    <form action="card_details.php?id=<?php echo $card->id; ?>" method="POST">
                        <input type="submit" name="edit" value="Edit" class="btn btn-block inline-button base-style z-depth-0">
                    </form>
                </div>
                <div class="col s4 m3 l2">
                    <?php if($delete == 'n') : ?>
                        <form action="card_details.php?id=<?php echo $card->id; ?>" method="POST">
                            <input type="submit" name="delete" value="Delete" class="btn btn-block inline-button base-style z-depth-0">
                        </form>
                    <?php elseif($delete == 'y') : ?>
                        <form action="card_details.php?id=<?php echo $card->id; ?>" method="POST">
                            <input type="submit" name="confirm" value="Confirm" class="btn btn-block inline-button red z-depth-0">
                        </form>
                    <?php endif ?>
                </div>
            </div>
            <div class="row"> 
                <div class="center">
                    <a href="lesson_details.php?id=<?php echo $card->lesson; ?>">
                        <div class="btn grey inline-button z-depth-0">Back to Lesson</div>
                    </a>
				</div>
            </div>
        </div>

        <?php else: ?>
            <h4 class="center red-text">No such card exists.</h4>
        <?php endif ?>

        <?php include('templates/footer.php'); ?>
    </div>
</html>
