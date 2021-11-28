<?php

namespace App\Controllers;

use App\Classes\PbnForm;
use App\Classes\PDO;
use App\Controllers\RankingsRepository;
use App\Controllers\TravellersRepository;
use Exception;
use HttpInvalidParamException;
use PDOException;

class ReadPbnController extends PbnForm
{
    // The file will always start reading at this line.
    private $contestDate;
    private $tScoreTseekNum;
    private $initialData = [];

    private $rankings;
    private $travellers;
    private const VIEW = VIEW_PATH . '/readPbn/indexView.php';

    private $rankingsController;
    /**
     * @var TravellersRepository
     */

    private $travellersController;

    function __construct()
    {
        parent::__construct();
        $this->rankingsController = new RankingsRepository();
        $this->travellersController = new TravellersRepository();
    }

    /**
     * @return View
     */
    public function index()
    {
        return include self::VIEW;
    }

    /**
     * @param $post
     * @param $files
     */
    public function post($post, $files)
    {
        // Devolveremos un mensaje a la vista.
        $message = [];
        //declarations
        $fileIsValid = false;
        try {
            if(empty($post['torneo']))
                return header('Location:'.SERVER_URL."index.php/pbn");
            $torneo = (int)$post['torneo'];


            // Obtenemos el fichero.
            $pbn = $this->formatter->getFile($files['file_pbn']['tmp_name']);
            if (empty($pbn))
                throw new HttpInvalidParamException("No se ha podido encontrar el archivo especificado");
            // comprobamos que es un fichero pbn válido.
            $fileIsValid = $this->validateFile($pbn);
            if (!$fileIsValid)
                throw new Exception("El archivo PBN no es válido.");

            // eliminamos los rankings ya existentes.
            if ($this->rankingsController->deleteRankings($torneo))
                $this->rankings = $this->rankingsController->getRankings($pbn, $this->tScoreTseekNum, $torneo);

            if (gettype($this->rankings) != "array")
                throw new Exception("No se pudo leer la tabla de rankings.");

            if ($this->travellersController->deleteHands($torneo))
                $this->travellers = $this->travellersController->getTravellers($pbn, $this->rankingsController->getScoreTseekNum(), $this->initialData);
            if (gettype($this->travellers) != "array")
                throw new Exception("No se pudieron conseguir los travellers.");

            // insert data in db.
            $this->pdoConnection->beginTransaction();
            // Rankings.
            $rankingsInsert = $this->rankingsController->insertRankings($this->rankings);
            // Travellers
            $travellersInsert = $this->travellersController->insertTravellers($this->travellers, $torneo);
            if ($rankingsInsert === false && $travellersInsert === false)
                throw new PDOException("Error en la inserción de datos.");

            $commit = $this->pdoConnection->commit();
            if (!$commit)
                throw new PDOException("Error a la hora de introducir los datos.");
            $message['message'] = "Los datos se insertaron con éxito";
            $message['status'] = 'ok';
        } catch (PDOException $e) {
            $message['status'] = 'error';
            if (!empty($pdoConnection)) {
                $pdoConnection->rollBack();
                $message['message'] = "Database Code Error: {$pdoConnection->errorCode()}: ";
                foreach ($pdoConnection->errorInfo() as $error) {
                    $message['message'] .= "$error <br />";
                }
                $message['message'] .= $e->getMessage();
            }
            $message['message'] .= $e->getMessage();
        } catch (Exception $e) {
            $message['status'] = 'error';
            $message['message'] .= $e->getMessage();
        }

        return include self::VIEW;
    }

    /**
     * @param \SplFileObject $file
     * @return bool
     */
    private function validateFile(\SplFileObject $file)
    {
        $totalScoreTable = false;
        $scoreTable = false;
        $optimumResultTable = false;
        $board = false;
        $date = false;
        $dealer = false;
        $file->seek(self::START_LINE);
        foreach ($file as $line) {
            if (empty($this->contestDate)) {
                $date = \strpos($line, self::START_DATE_NEEDLE);
                if ($date != false) {
                    $line = \explode('content="', $file->current())[1];
                    $line = \str_replace(['"', "\'", '>', ' '], '', $line);
                    $this->contestDate = $line;
                }
            }
            if (isset($this->contestDate)) {
                // Check if $totalScoreTable still false.
                if ($totalScoreTable === false)  // if the strpos is false still with the same value, if not, $equals to line.
                    $totalScoreTable = \strpos($line, self::TOTAL_SCORE_TABLE_NEEDLE) === false ? $totalScoreTable : $line;
                if ($scoreTable === false)
                    $scoreTable = \strpos($line, self::SCORE_TABLE) === false ? $scoreTable : $line;
                if ($optimumResultTable === false)
                    $optimumResultTable = \strpos($line, self::OPTIMUM_RESULT_NEEDLE) === false ? $optimumResultTable : $line;
                // The value of "totalScoreTable" is always "$file->key()" + 1.
                // (if we put this line of code on the top of the script we don't need the +1 and we will have the same result).
                if ($totalScoreTable != false && empty($this->tScoreTseekNum))
                    $this->tScoreTseekNum = $file->key() + 1;

                //Get initial data
                if (empty($this->initialData['traveller']['date']))
                    $this->initialData['traveller']['date'] = \strpos($line, self::DATE_NEEDLE) === false ? null : trim(\str_replace([self::DATE_NEEDLE, ']', '"'], '', $line));
                if (empty($this->initialData['traveller']['board']))
                    $this->initialData['traveller']['board'] = \strpos($line, self::BOARD_NEEDLE) === false ? null : trim(\str_replace([self::BOARD_NEEDLE, ']', '"'], '', $line));
                if (empty($this->initialData['hand']['dealer']))
                    $this->initialData['hand']['dealer'] = \strpos($line, self::DEALER_NEEDLE) === false ? null : $line;
                if (empty($this->initialData['hand']['vulnerable']))
                    $this->initialData['hand']['vulnerable'] = \strpos($line, self::VULNERABLE_NEEDLE) === false ? null : $line;
                if (empty($this->initialData['hand']['deal']))
                    $this->initialData['hand']['deal'] = \strpos($line, self::DEAL_NEEDLE) === false ? null : $line;

            }
            // salir foreach con break o lo que sea.
        }

        return !empty($totalScoreTable) && !empty($scoreTable) && !empty($optimumResultTable);
    }
}
