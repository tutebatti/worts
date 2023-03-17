<?php
	$server = 'localhost';
	$user = 'some_user';
	$pw = 'some_pw';
	$db = 'worts_db';
	$charset = 'utf8';
	
	// connect to database
	try
	{
		$pdo = new PDO('mysql:host=' . $server . ';dbname=' . $db . ';charset=' . $charset . ';', $user, $pw);
	}
	catch(PDOException $e)
	{
		exit('Unable to connect to database.');
	}

	function pdo($pdo, $sql, $args = NULL)
    {
        if (!$args)
        {
            return $pdo->query($sql);
        }
        $stmt = $pdo->prepare($sql);
        $stmt->execute($args);
        return $stmt;
    }
?>