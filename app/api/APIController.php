<?php

namespace App\API;

use App\database\DatabaseController;

class APIController
{
    public function requestFunction()
    {
        if(isset($_GET['getwords']))
        {
            $this->getList();
        }
        elseif(isset($_GET['delete'])) {

            $this->delete();

        }elseif(isset($_GET['update'])){

           echo "Cant update"; //Nothing to update???

        }elseif(isset($_GET['create']))
        {
            $this->create();
        }
        else{
            http_response_code(400);
        }
    }

    private function getList()
    {
        $dbControl = new DatabaseController();
        $list = $dbControl->getHyphenedWords();
        print_r($list);

        http_response_code(200);
    }

    private function delete()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST'&&isset($_POST['ID'])){
            $dbController = new DatabaseController();
            $dbController->deleteWordWhereID($_POST["ID"]);

            http_response_code(200);
        }

        http_response_code(404);
    }

    private function create()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST'&&isset($_POST['word'])){
            $dbController = new DatabaseController();
            $dbController->insertOneWord($_POST['word']);
            http_response_code(201);
        }
        http_response_code(404);
    }
}