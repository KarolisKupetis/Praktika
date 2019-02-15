<?php
$data = array(
    array('name' => 'John', 'age' => '25'),
    array('name' => 'Wendy', 'age' => '32')
);

try {
    $pdo = new PDO('sqlite:myfile.sqlite');
}

catch(PDOException $e) {
    die('Unable to open database connection');
}

$insertStatement = $pdo->prepare('insert into mytable (name, age) values (:name, :age)');

// start transaction
$pdo->beginTransaction();

foreach($data as &$row) {
    $insertStatement->execute($row);
}

// end transaction
$pdo->commit();

?>
