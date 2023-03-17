<?php
	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(E_ALL);

	include('./config/db_connect.php');
	include('./config/classes.php');

    if(isset($_GET['id']))
    {
        $session = new StudySession($pdo, $_GET['id']);
        
        if(isset($_POST['done']))
        {
            header('Location: index.php');
        }
    }

?>
<!DOCTYPE html>
<html>
    <?php include('templates/header.php'); ?>
    
    <?php if($session): ?>
        <section class="container grey-text">
            
            <h4 class="center">Summary</h4>
            
            <form class="white adding" action="study_result.php?id=<?php echo $session->id; ?>" method="POST">
                <div class="row">
                    <?php
                        foreach(array(
                            'Cards studied'=>count($session->cards),
                            'Correct'=>count($session->correct_cards) . ' / ' . count($session->correct_cards)/count($session->cards)*100 . ' %',
                            'Incorrect'=>count($session->incorrect_cards) . ' / ' . count($session->incorrect_cards)/count($session->cards)*100 . ' %',
                            ) as $key=>$value){ ?>

                                <div class="input-field col s12">
                                    <h6><?php echo $key; ?></h6>
                                    <input
                                        type="text"
                                        class="black-text"
                                        value="<?php echo $value; ?>"
                                        disabled
                                    >
                                </div>
                    <?php } ?>
                    <div class="col s4 offset-s4">
                        <input type="submit" name="done" value="OK" class="btn base-style inline-button z-depth-0"
                    </div>
                </div>
            </form>
        </section>

        <section class="container grey-text center">
            <div class="row">
                <div class="col s12">
                    <h4>Detailed List</h4>
                </div>
                <div class="col s12 m6">
                    <div class="row">
                        <h6>Correct</h6>
                        <?php
                        foreach($session->correct_cards as $card_id)
                        { $card = new Flashcard($pdo, $card_id) ?>
                        <div class="col s12 center">
                            <div class="card z-depth-0">
                                <div class="card-content">
                                    <h6 class="black-text">
                                        <?php echo $card->front; ?>
                                    </h6>
                                    <br>
                                    <span class="btn-floating level-<?php echo $card->level; ?>">
                                        <?php echo $card->level; ?>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <?php } ?>
                    </div>
                </div>
                <div class="col s12 m6">
                    <div class="row">
                        <h6>Incorrect</h6>
                        <?php
                        foreach($session->incorrect_cards as $card_id)
                        { $card = new Flashcard($pdo, $card_id) ?>
                        <div class="col s12 center">
                            <div class="card z-depth-0">
                                <div class="card-content">
                                    <h6 class="black-text">
                                        <?php echo $card->front; ?>
                                    </h6>
                                    <br>
                                    <span class="btn-floating level-<?php echo $card->level; ?>">
                                        <?php echo $card->level; ?>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </section>

    <?php else: ?>
        <h4 class="center red-text">No study session completed.</h4>
    <?php endif ?>

    <?php include('templates/footer.php'); ?>
</html>