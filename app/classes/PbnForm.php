<?php

namespace App\Classes;

use App\Classes\Formatter;
use App\Classes\PdoClass;


class PbnForm
{
    /**
     * @var PDO
     */
    protected $pdoConnection;
    /**
     * @var PDO
     */
    protected $formatter;

    public function __construct()
    {
        $this->pdoConnection = PdoClass::getInstance();
        $this->formatter = new Formatter();
    }

    protected const LINES_TO_JUMP = 22;
    protected const BOARD_NEEDLE = '[Board ';
    protected const DEALER_NEEDLE = '[Dealer ';
    protected const DATE_NEEDLE = '[EventDate ';// No sé si es date o event date, por si hay varios días de torneos.
    protected const OPTIMUM_SCORE_NEEDLE = '[OptimumScore ';
    protected const OPTIMUM_CONTRACT_NEEDLE = '[OptimumContract ';
    protected const VULNERABLE_NEEDLE = '[Vulnerable ';
    protected const DEAL_NEEDLE = '[Deal ';
    protected const SCORE_TABLE = '[ScoreTable ';
    protected const START_LINE = 12;
    protected const START_DATE_NEEDLE = '<META  name=ContestDate ';
    protected const TOTAL_SCORE_TABLE_NEEDLE = '[TotalScoreTable ';
    protected const OPTIMUM_RESULT_NEEDLE = '[OptimumResultTable ';
}
