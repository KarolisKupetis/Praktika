<?php

namespace App\API;

use App\database\DatabaseController;
use Psr\Log\LoggerInterface;

class APIController
{
    public function requestFunction()
    {
        $calledMethod = $_SERVER['REQUEST_METHOD'];

        switch ($calledMethod) {

            case 'POST':
                $this->create();
                break;

            case 'GET':
                $list = $this->getData();
                print_r($list);
                http_response_code(200);
                break;

            case 'PUT':
                $this->updateData();
                break;

            case 'DELETE':
                $this->deleteData();
                break;

        }
    }

    private function getData()
    {
        $dbControl = new DatabaseController();
        return $dbControl->getHyphenedWords();

    }

    private function deleteData()
    {
        parse_str(file_get_contents('php://input'), $_DELETE);
        echo $_DELETE['ID'];
        $dbController = new DatabaseController();
        //$dbController->deleteWordWhereID($_DELETE['ID']);

        //Papildyti po modelio sukurimo
        http_response_code(404);
    }

    private function create()
    {
        if (isset($_POST['word'])) {
            $dbController = new DatabaseController();
            $dbController->insertOneWord($_POST['word']);
            http_response_code(201);
        }

        http_response_code(404);
    }

    private function updateData()
    {
        parse_str(file_get_contents('php://input'), $_PUT);
        echo $_PUT['word'];

        //Papildyti po modelio sukurimo.
    }
}