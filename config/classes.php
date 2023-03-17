<?php
    class Flashcard
	{
		public $id;
		public $lesson;
		public $level;
		public $front;
		public $back;
		public $note;
		public $created_at;
		protected $pdo;

		public function __construct($pdo, $id)
		{
			$this->pdo = $pdo;
			$sql = 'SELECT * FROM flashcards WHERE id = ?';
			$card = pdo($this->pdo, $sql, [$id])->fetch();
			$this->id = $card['id'];
			$this->lesson = $card['lesson'];
			$this->level = $card['level'];
			$this->front = $card['front'];
			$this->back = $card['back'];
			$this->note = $card['note'];
			$this->created_at = $card['created_at'];
		}

		public function update($column, $value)
		{
			$sql = "UPDATE flashcards SET $column = ? WHERE id = ?";
			pdo($this->pdo, $sql, [$value, $this->id]);
			$this->$column = $value;
		}
	}

	class Lesson
	{
		public $id;
		public $title;
		public $note;
		public $created_at;
		protected $pdo;

		public function __construct($pdo, $id)
		{
			$this->pdo = $pdo;
			$sql = 'SELECT * FROM lessons WHERE id = ?';
			$lesson = pdo($this->pdo, $sql, [$id])->fetch();
			$this->id = $lesson['id'];
			$this->title = $lesson['title'];
			$this->note = $lesson['note'];
			$this->created_at = $lesson['created_at'];
		}
		public function update($column, $value)
		{
			$sql = "UPDATE lessons SET $column = ? WHERE id = ?";
			pdo($this->pdo, $sql, [$value, $this->id]);
			$this->$column = $value;
		}

		public function card_ids()
		{
			$sql = 'SELECT id FROM flashcards WHERE lesson = ?';
			return pdo($this->pdo, $sql, [$this->id])->fetchAll(PDO::FETCH_COLUMN, 0);
		}
		public function card_count()
		{
			$sql = 'SELECT count(*) FROM flashcards WHERE lesson = ?';
			return pdo($this->pdo, $sql, [$this->id])->fetchColumn();
		}
		public function average_level()
		{
			$sql = 'SELECT avg(level) FROM flashcards WHERE lesson = ?';
			return number_format(pdo($this->pdo, $sql, [$this->id])->fetchColumn(), 1);
		}
	}

	class StudySession
	{
		public $id;
		public $idx;
		public $cards;
		public $inverted;
		public $correct_cards;
		public $incorrect_cards;
		protected $pdo;

		public function __construct($pdo, $id)
		{
			$this->pdo = $pdo;
			$sql = 'SELECT * FROM studysessions WHERE id = ?';
			$session = pdo($this->pdo, $sql, [$id])->fetch();
			$this->id = $session['id'];
			$this->idx = $session['idx'];
			$this->cards = json_decode($session['cards']);
			$this->inverted = $session['inverted'];
			$this->correct_cards = json_decode($session['correct_cards']);
			$this->incorrect_cards = json_decode($session['incorrect_cards']);
		}
		
		public function store_result($correctness, $card_id)
		{
			if ($correctness == 'correct')
			{
				$this->correct_cards[] = $card_id;
			}
			else
			{
				$this->incorrect_cards[] = $card_id;
			}

			$sql = 'UPDATE studysessions SET correct_cards = ?, incorrect_cards = ? WHERE id = ?';
			$params = [json_encode($this->correct_cards), json_encode($this->incorrect_cards), $this->id];
			pdo($this->pdo, $sql, $params);
		}

		public function update_idx()
		{
			$this->idx += 1;
			$sql = 'UPDATE studysessions SET idx = ? WHERE id = ?';
			pdo($this->pdo, $sql, [$this->idx, $this->id]);
		}
	}

	class user
	{
		public $id;
		public $name;
		public $pw;

		public function __construct($pdo, $id)
		{

		}

	}
?>