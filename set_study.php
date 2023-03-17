<?php
	// ini_set('display_errors', 1);
	// ini_set('display_startup_errors', 1);
	// error_reporting(E_ALL);

	include('./config/db_connect.php');
	include('./config/classes.php');

    $lesson_ids = pdo($pdo, 'SELECT id FROM lessons')->fetchAll(PDO::FETCH_COLUMN, 0);
    
    $number_of_cards_field = '30';
    $lessons_chosen = $levels_chosen = [];
    $inverted = 'n';

    $errors = array('number_error'=>'','lesson_error'=>'','level_error'=>'');

    if(isset($_POST['start']))
    {
        $number_of_cards_field = $_POST['number_of_cards_field'];
        $lessons_chosen = $_POST['lessons_chosen'];
        $levels_chosen = $_POST['levels_chosen'];
        $inverted = $_POST['inverted'] == 'y' ? 'y': 'n';

        if(!filter_var($number_of_cards_field, FILTER_VALIDATE_INT))
        {
            $errors['number_error'] = "Must be a whole number.";
        }
        if(empty($_POST['lessons_chosen']))
        {
            $errors['lesson_error'] = "Choose at least one lesson.";
            $lessons_chosen = [];
        }
        if(empty($_POST['levels_chosen']))
        {
            $errors['level_error'] = "Choose at least one level.";
            $levels_chosen = [];
        }
        if(!array_filter($errors))
        {
            try
            {
                $lessons_filler = str_repeat('lesson = ? OR ', count($lessons_chosen) -1) . 'lesson = ?';
                $levels_filler = str_repeat('level = ? OR ', count($levels_chosen) -1) . 'level = ?';
                
                $sql = "SELECT id FROM flashcards WHERE ($lessons_filler) AND ($levels_filler) ORDER BY RAND() LIMIT $number_of_cards_field";
                
                $result = pdo($pdo, $sql, array_merge($lessons_chosen, $levels_chosen))->fetchAll(PDO::FETCH_COLUMN, 0);

            }
            catch(PDOException $e)
			{
				exit('Unable to fetch data from database.');
			}

            $sql = 'INSERT
				INTO studysessions(cards, correct_cards, incorrect_cards, inverted)
				VALUES (?, ?, ?, ?)';
			$params = [json_encode($result), json_encode([]),json_encode([]), $inverted];
			pdo($pdo, $sql, $params);
			
            header('Location: study.php?id=' . $pdo->lastInsertId());
        }
    }

?>
<!DOCTYPE html>
<html>
	<?php include('templates/header.php'); ?>

    <section class="container grey-text">
        
		<h4 class="center">Study Vocab</h4>
        
        <form class="white adding" action="set_study.php" method="POST">
            <div class="row">

                <div class="input-field col s12 m5">
                    <h6>Number of Cards</h6>
                    <input type="text" name="number_of_cards_field" value="<?php echo $number_of_cards_field; ?>">
                    <div class="red-text"><?php echo $errors['number_error']; ?></div>
                </div>
                
                <div class="input-field col s12 m5 push-m1">
                    <h6>Level</h6>
                                            
                        <?php foreach(range(1, 5) as $level){ ?>
                            
                            <p>
                                <label>
                                    <input
                                        type="checkbox"
                                        name="levels_chosen[]"
                                        checked
                                        value="<?php echo $level; ?>" 
                                        <?php if(in_array($level, $levels_chosen)){echo 'checked';} ?>
                                    >
                                    <span>
                                        <?php echo $level; ?>
                                    </span>
                                </label>
                            </p>
                            
                        <?php } ?>
                    <div class="red-text"><?php echo $errors['level_error']; ?></div>
                </div>

                <div class="input-field col s12">
                    <h6>Lesson</h6>
                                            
                        <?php foreach($lesson_ids as $lesson_id){ $lesson = new Lesson($pdo, $lesson_id) ?>
                            
                            <p>
                                <label>
                                    <input
                                        type="checkbox"
                                        name="lessons_chosen[]"
                                        value="<?php echo $lesson->id; ?>" 
                                        <?php if(in_array($lesson->id, $lessons_chosen)){echo 'checked';} ?>
                                    >
                                    <span>
                                        <?php echo $lesson->title; ?>
                                    </span>
                                </label>
                            </p>
                            
                        <?php } ?>
                    <div class="red-text"><?php echo $errors['lesson_error']; ?></div>
                </div>

                <div class="input-field col s12">
                    <h6>Inverted</h6>
                    <p>
                        <label>
                            <input type="checkbox" name="inverted" value="y"
                                <?php if($inverted == 'y'){echo 'checked';} ?>
                            >
                            <span>User needs to input front instead of back</span>
                        </label>
                    </p>
                </div>

                <div class="input-field col s12 center">
                    <input type="submit" name="start" value="Start" class="btn base-style z-depth-0">
                </div>
            </div>
        </form>
    </section>

    <?php include('templates/footer.php'); ?>
</html>
