<?php
	// ini_set('display_errors', 1);
	// ini_set('display_startup_errors', 1);
	// error_reporting(E_ALL);

	include('./config/db_connect.php');
	include('./config/classes.php');

    if(isset($_GET['id']))
    {
        $delete = 'n';
        
        $lesson = new Lesson($pdo, $_GET['id']);

        if(isset($_POST['edit']))
        {
            header('Location: edit_lesson.php?id=' . $lesson->id);
        }
        
        if(isset($_POST['delete']))
        {
            $delete = 'y';
        }

        if(isset($_POST['confirm']))
        {
            // 2do: double check with alert
            // 2do: cannot delete if not empty
            try
            {
                $sql = 'DELETE FROM flashcards WHERE lesson = ?';
                pdo($pdo, $sql, [$lesson->id]);
                
                $sql = 'DELETE FROM lessons WHERE id = ?';
                pdo($pdo, $sql, [$lesson->id]);
                
                header('Location: index.php');
            }
            catch(PDOException $e)
            {
                exit('Unable to delete lesson because not empty.');
            }
        }
    }
?>
<!DOCTYPE html>
<html>
	<?php include('templates/header.php'); ?>

    <div class="container grey-text">
        <?php if($lesson): ?>
            <h4 class="center black-text">
                <?php echo $lesson->title; ?>
            </h4>
            <h6 class="center">
                <?php echo $lesson->note; ?>
            </h6>
            
            <!-- Buttons to edit, delete -->
            <div class="row">
                <div class="row">
                    <div class="col s6 m3 offset-m3 l2 offset-l4">
                        <form action="lesson_details.php?id=<?php echo $lesson->id; ?>" method="POST">
                            <input type="submit" name="edit" value="Edit" class="btn btn-block inline-button base-style z-depth-0">
                        </form>
                    </div>
                    <?php if($delete == 'n') : ?>
                        <div class="col s6 m3 l2">
                            <form action="lesson_details.php?id=<?php echo $lesson->id; ?>" method="POST">
                                <input type="submit" name="delete" value="Delete" class="btn btn-block inline-button base-style z-depth-0">
                            </form>
                        </div>
                    <?php elseif($delete == 'y') :?>
                        <div class="col s6 m3 l2">
                            <form action="lesson_details.php?id=<?php echo $lesson->id; ?>" method="POST">
                                <input type="submit" name="confirm" value="Confirm" class="btn btn-block inline-button red z-depth-0">
                            </form>
                        </div>
                        <div class="col s12">
                            <h6 class="center red-text">All cards will be deleted as well!</h6>
                        </div>
                    <?php endif ?>
                </div>
            </div>
            
            <!-- Lesson data -->
            <div class="row">
                <?php
                    foreach(array(
                        'ID'=>$lesson->id,
                        'Created on'=>date("M d Y", strtotime($lesson->created_at)),
                        'Number of Cards'=>$lesson->card_count(),
                        'Average Level'=>$lesson->average_level()
                        ) as $key=>$value){ ?>
                             
                            <div class="input-field col s6 m4 l3">
                            <h6><?php echo $key; ?></h6>
                            <input
                                type="text"
                                class="black-text"
                                value="<?php echo $value; ?>"
                                disabled
                            >
                            </div>
                <?php } ?>
            </div>
            
            <!-- List of Cards -->
            <div class="row">

                <!-- Add card -->
                <div class="col s12 m6 l4 center">
                    <a href="add_card.php?lesson_id=<?php echo $lesson->id; ?>" style="display:block;">
                        <div class="card tiny base-style z-depth-0 white-text">
                            <div class="card-content">
                                <h5 style="padding-top:1.5em;">+ Add a card</h5>
                            </div>
                        </div>
                    </a>
                </div>
            
                <!-- Loop over all cards in lesson -->
                <?php
                    foreach($lesson->card_ids() as $card_id)
                    {
                        $card = new Flashcard($pdo, $card_id)
                ?>

                    <div class="col s12 m6 l4 center">
                        <div class="card tiny z-depth-0">
                            <div class="btn-floating base-style level level-<?php echo $card->level; ?>">
                                <?php echo $card->level; ?>
                            </div>
                            <div class="card-content">
                                <h5 class="black-text">
                                    <?php echo $card->front; ?>
                                </h5>
                                <p class="grey-text">
                                    <?php echo $card->note; ?>
                                </p>
                            </div>
                            <div class="card-action right-align">
                                <a class="base-text-style" href="card_details.php?id=<?php echo $card->id ?>">Go to card</a>
                            </div>
                        </div>
                    </div>

                <?php
                    }
                ?>
            </div>
            
        <?php else: ?>
            <h4 class="red-text center">No such lesson exists.</h4>
        <?php endif ?>
    </div>

    <?php include('templates/footer.php'); ?>
</html>
