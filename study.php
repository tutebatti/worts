<?php
	// ini_set('display_errors', 1);
	// ini_set('display_startup_errors', 1);
	// error_reporting(E_ALL);

	include('./config/db_connect.php');
	include('./config/classes.php');

    if(isset($_GET['id']))
    {
        $session = new StudySession($pdo, $_GET['id']);
        var_dump($session->inverted);
        $state = 'testing';

        if(isset($_POST['next_card']))
        {
            $session->update_idx();
        }

        if($session->idx >= count($session->cards))
        {
            header('Location: study_result?id=' . $session->id);
        }

        $card = new Flashcard($pdo, $session->cards[$session->idx]);
        $lesson = new Lesson($pdo, $card->lesson);
        
        if(isset($_POST['check']))
        {
            if(!in_array($card->id, array_merge($session->correct_cards, $session->incorrect_cards)))
            {
                $state = "checking";

                $correct_answer = $session->inverted == 'y' ? $card->front: $card->back;

                if($_POST['back_field'] == $correct_answer)
                {
                    $new_level = $card->level > 1 ? $card->level - 1 : 1;
                    $correctness = 'correct';
                }
                else
                {
                    $new_level = $card->level < 5 ? $card->level + 1 : 5;
                    $correctness = 'incorrect';
                }
                
                $card->update('level', $new_level);
                $session->store_result($correctness, $card->id);
            }
        }
    }
?>
<!DOCTYPE html>
<html>
    <?php include('templates/header.php'); ?>
    
    <div class="container center">
        <?php if($session): ?>
            <form action="study.php?id=<?php echo $session->id; ?>" method="POST">
                <div class="row">
                    <div class="col s12 m8 offset-m2 l6 offset-l3">
                        <div class="card">
                            <!-- level button in upper right corner -->
                            <div class="btn-floating base-style level level-<?php echo $card->level; ?>">
                                <?php echo $card->level; ?>
                            </div>
                            <div class="card-content">
                                <div class="row">
                                    <div class="col s12">
                                        <!-- front -->
                                        <h4>
                                            <?php
                                                if($session->inverted == 'y')
                                                {
                                                    echo $card->back;
                                                }
                                                else
                                                {
                                                    echo $card->front;
                                                }
                                            ?>
                                        </h4>
                                        <hr style="width:30%;"/>
                                        <!-- back -->
                                        <div class="row">
                                            <div class="col s4 offset-s4 center">
                                                <input
                                                    class="quiz-input"
                                                    type="text"
                                                    name="back_field"
                                                    autocomplete="off"
                                                <?php if($state == 'testing'): ?>
                                                    autofocus="autofocus" 
                                                <?php elseif($state == 'checking'): ?>
                                                    value="<?php echo $_POST['back_field']; ?>"
                                                    disabled
                                                    style=
                                                    <?php if($correctness == 'correct'): ?>
                                                        "color: yellowgreen;"
                                                    <?php elseif($correctness == 'incorrect'): ?>
                                                        "color: red; text-decoration: line-through;"
                                                >
                                            </div>
                                            <div class="col s4 offset-s4 center">
                                                <h4>
                                                    <span>≠ </span> 
                                                    <span style="color:yellowgreen;">
                                                        <?php
                                                            if($session->inverted == 'y')
                                                            {
                                                                echo $card->front;
                                                            }
                                                            else
                                                            {
                                                                echo $card->back;
                                                            }
                                                        ?>
                                                    </span>
                                                </h4>
                                                    <?php endif ?>
                                                <?php endif ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col 12">
                                        <h5 class="grey-text">
                                            <?php echo $card->note;?>
                                        </h5>
                                    </div>
                                    <div class="col s12">
                                        <h6>
                                            <b>Lesson:</b> <?php echo $lesson->title; ?>
                                        </h6>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col s2 offset-s5">
                    <?php if($state == 'testing'): ?>
                        <input type="submit" name="check" value="Check" class="btn btn-block inline-button base-style z-depth-0">
                    <?php elseif($state == 'checking'): ?>
                        <input type="submit" name="next_card" value="Next card" autofocus="autofocus" class="btn btn-block inline-button base-style z-depth-0">
                    <?php endif ?>
                    </div>
                </div>
            </form>
        <?php else: ?>
            <h4 class="center red-text">No study session set up.</h4>
        <?php endif ?>
                
        <?php include('templates/footer.php'); ?>
    </div>
</html>