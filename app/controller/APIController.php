<?php

namespace App\Controller;

use App\database\models\HyphenedWordsModel;
use App\database\WordsModel;
use Psr\Log\LoggerInterface;

class APIController
{
    private $wordsModel;
    private $hyphenatedWordsModel;

    public function __construct()
    {
        $this->wordsModel = new WordsModel();
        $this->hyphenatedWordsModel = new HyphenedWordsModel();
    }

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
                http_response_code(200);
                break;

            case 'DELETE':
                $this->deleteData();
                http_response_code(200);
                break;

        }
    }

    private function getData()
    {
        $result = $this->hyphenatedWordsModel->getAllHyphenedWords();

        return $result;
    }

    private function deleteData()
    {
        parse_str(file_get_contents('php://input'), $_DELETE);
        $wordsId = $_DELETE['ID'];
        $this->wordsModel->deleteWordWhereID($wordsId);
    }

    private function create()
    {
        if (isset($_POST['word'])) {
            $word = $_POST['word'];
            $this->wordsModel->insertOneWord($word);
            http_response_code(201);
        } else {
            http_response_code(404);
        }
    }

    private function updateData()
    {
        parse_str(file_get_contents('php://input'), $_PUT);
        $newWord = $_PUT['word'];
        $wordId = $_PUT['ID'];
        $this->wordsModel->updateWordWhereID($wordId,$newWord);
    }
}