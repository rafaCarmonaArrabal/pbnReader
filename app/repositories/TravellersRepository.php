<?php

namespace App\Controllers;

use Exception;
use HttpInvalidParamException;
use App\Classes\PbnForm;
use App\Classes\PDO;
use PDOException;

class TravellersRepository extends PbnForm
{
    public function getTravellers(\SplFileObject $file, int $seekNumber, array $initialData, $travellers = null)
    {
        $traveller = [];
        if (empty($travellers)) {
            $travellers = [];
        }
        $hand = [];
        $file->seek($seekNumber);
        if (\strpos($file->current(), self::SCORE_TABLE) !== false && count($travellers) === 0) {
            $file->next();
            $traveller = array_merge($traveller, $initialData['traveller']);
            $hand = array_merge($hand, $initialData['hand']);
        } else {
            // buscará la siguiente tabla ScoreTable. Al terminar, hará un next para que empiece a contar.
            do {
                $line = $file->current();
                // guardamos los date, boards y dealer.
                if (empty($traveller['date']))
                    $traveller['date'] = \strpos($line, self::DATE_NEEDLE) === false ? null : trim(\str_replace([self::DATE_NEEDLE, ']', '"'], '', $line));;
                if (empty($traveller['board']))
                    $traveller['board'] = \strpos($line, self::BOARD_NEEDLE) === false ? null : trim(\str_replace([self::BOARD_NEEDLE, ']', '"'], '', $line));
                if (empty($hand['dealer']))
                    $hand['dealer'] = \strpos($line, self::DEALER_NEEDLE) === false ? null : $line;
                if (empty($hand['vulnerable']))
                    $hand['vulnerable'] = \strpos($line, self::VULNERABLE_NEEDLE) === false ? null : $line;
                if (empty($hand['deal']))
                    $hand['deal'] = \strpos($line, self::DEAL_NEEDLE) === false ? null : $line;
                $file->next();
            } while (\strpos($file->current(), self::SCORE_TABLE) === false);

            // pasamos al siguiente ScoreTable.
            $file->next();
        }
        // Recogemos los travellers
        do {
            // t0do esto se puede hacer con una expresion regular sacando todos los titulos que hay en una score table. Luego lo pasamos a array y con un array map recorremos la line y poniendo de key los valores del array con los nombres de la scoreTable. Lo mismo con la tabla de los rankings.
            if (!empty($file->current())) {
                $line = $this->formatter->stringToArray($file->current(), false);
                $traveller['scoreTables'][] = $this->getTravellersArray($line);
            }
            $file->next();
        } while (\strpos($file->current(), self::OPTIMUM_SCORE_NEEDLE) === false);
        // End the hand array.
        do {
            $line = $file->current();
            if (empty($hand['optimumScore']))
                $hand['optimumScore'] = \strpos($line, self::OPTIMUM_SCORE_NEEDLE) === false ? null : trim(\str_replace(['NS ', '"'], '', $line));
            if (empty($hand['optimumContract']))
                $hand['optimumContract'] = \strpos($line, self::OPTIMUM_CONTRACT_NEEDLE) === false ? null : $line;
            $file->next();
        } while (\strpos($file->current(), self::OPTIMUM_RESULT_NEEDLE) === false);

        $file->seek($file->key() + self::LINES_TO_JUMP);
        $traveller['hand'] = $this->formatHand($hand);
        // pasamos al siguiente ScoreTable.
        $travellers[] = $traveller;
        if (!$file->eof()) {
            // Llamada recursiva.
            return $this->getTravellers($file, $file->key(), $initialData, $travellers);
        }

        return $travellers;
    }

    /**
     * @param array $line
     * @return array
     * get by parameter an array with the $line hand return a formated array.
     */
    private function getTravellersArray(array $line)
    {
        return [
            'section' => $line[0],
            'table' => $line[1],
            'round' => $line[2],
            'pairNSId' => $line[3],
            'pairEWId' => $line[4],
            'contract' => $line[5],
            'declarer' => $line[6],
            'result' => $line[7],
            'lead' => $line[8],
            'scoreNS' => $line[9],
            'scoreEW' => $line[10],
            'mpNS' => $line[11],
            'mpEW' => $line[12],
            'percentageNS' => $line[13],
            'percentageEW' => $line[14]
        ];
    }

    /**
     * @param array $hand
     * @return array
     */
    private function formatHand(array $hand)
    {
        $hand = array_map(function ($value) {
            return trim(\str_replace(['N:', 'E:', 'S:', 'W:', '"', ']', self::VULNERABLE_NEEDLE, self::DEAL_NEEDLE, self::DEALER_NEEDLE, self::OPTIMUM_CONTRACT_NEEDLE, self::OPTIMUM_SCORE_NEEDLE], '', $value));
        }, $hand);

        $dealers = ['N', 'E', 'S', 'W'];
        // get n, s, w, and e hands.
        $hands = \explode(" ", $hand['deal']);
        $position = \array_search($hand['dealer'], $dealers, false);
        $array1 = array_slice($dealers, 0, $position);
        $array2 = array_slice($dealers, $position, \count($dealers));

        $dealers = !empty($array1) ? array_merge($array2, $array1) : $dealers;

        $hand['deal'] = array_combine($dealers, $hands);

        return $hand;
    }

    /**
     * TODO
     * @param array $travellers
     * @param int $torneo
     */
    public function insertTravellers(array $travellers, int $torneo)
    {
        // get the ranking_id.
        $travellerQuery = "INSERT INTO travellers (pairNS_id, pairEW_id, board, p_table, round, contract, result, 
                        declarer, lead, score_ns, score_ew, mp_ns, mp_ew, percentage_ns, percentage_ew, date)
            VALUES (:pairNS_id, :pairEW_id, :board, :tabla, :round, :contract, :result, :declarer, :lead, :score_ns, :score_ew, :mp_ns, :mp_ew, :percentage_ns, :percentage_ew, :date)";

        $travellerQ = $this->pdoConnection->prepare($travellerQuery);
        $handsInsert = false;
        foreach ($travellers as $traveller) {
            $pairEWId = null;
            $pairNSId = null;
            $mpNS = 0.0;
            $mpEW = 0.0;
            $percentageNS = 0.0;
            $percentageEW = 0.0;
            foreach ($traveller['scoreTables'] as $scoreTable) {
                if (!empty($scoreTable['pairNSId']) && $scoreTable['pairNSId'] != '-') {
                    $pairNsQ = $this->pdoConnection->prepare("SELECT id FROM Rankings where pair_id = :pair_id AND tournament_id = :torneo");
                    $pairNsQ->execute([':pair_id' => $scoreTable['pairNSId'], ':torneo' => $torneo]);
                    $pairNSId = $pairNsQ->fetchColumn();
                }
                if (!empty($scoreTable['pairEWId']) && $scoreTable['pairEWId'] != '-') {
                    $pairEwQ = $this->pdoConnection->prepare("SELECT id FROM Rankings where pair_id = :pair_id AND tournament_id = :torneo");
                    $pairEwQ->execute([':pair_id' => $scoreTable['pairEWId'], ':torneo' => $torneo]);
                    $pairEWId = $pairEwQ->fetchColumn();
                }
                if (!empty($scoreTable['mpNS']) && $scoreTable['mpNS'] != '-')
                    $mpNS = $scoreTable['mpNS'];
                if (!empty($scoreTable['mpEW']) && $scoreTable['mpEW'] != '-')
                    $mpEW = $scoreTable['mpEW'];
                if (!empty($scoreTable['percentageNS']) && $scoreTable['percentageNS'] != '-')
                    $percentageNS = $scoreTable['percentageNS'];
                if (!empty($scoreTable['percentageEW']) && $scoreTable['percentageEW'] != '-')
                    $percentageEW = $scoreTable['percentageEW'];
                $travellerArray = [
                    ":pairNS_id" => $pairNSId ?? null,
                    ":pairEW_id" => $pairEWId ?? null,
                    ":board" => $traveller['board'],
                    ":tabla" => $scoreTable['table'],
                    ":round" => $scoreTable['round'],
                    ":contract" => $scoreTable['contract'],
                    ":result" => $scoreTable['result'],
                    ":declarer" => $scoreTable['declarer'],
                    ":lead" => $scoreTable['lead'],
                    ":score_ns" => $scoreTable['scoreNS'],
                    ":score_ew" => $scoreTable['scoreEW'],
                    ":mp_ns" => $mpNS,
                    ":mp_ew" => $mpEW,
                    ":percentage_ns" => $percentageNS,
                    ":percentage_ew" => $percentageEW,
                    ":date" => $traveller['date']
                ];

                $response = $travellerQ->execute($travellerArray);

                if ($response === false)
                    throw new PDOException("No se pudieron insertar los viajes con el los ids: NS => {$scoreTable['pairNSId']} EW => {$scoreTable['pairEWId']} en el torneo {$torneo}");
            }
            // insert hands
            $handsInsert = $this->insertHands($traveller['hand'], $torneo, $traveller['board']);
        }
        return $handsInsert;
    }

    public function insertHands(array $hand, int $tournament_id, int $board)
    {
        $handQuery = "INSERT INTO hands (tournament_id, vulnerable, board, deal_n, deal_e, deal_s, deal_w, optimum_score, optimum_contract, dealer)
                    VALUES (:tournament_id, :vulnerable, :board, :deal_n, :deal_e, :deal_s, :deal_w, :optimum_score, :optimum_contract, :dealer)";

        $handQ = $this->pdoConnection->prepare($handQuery);
        $handArray = [
            ":tournament_id" => $tournament_id,
            ":vulnerable" => $hand['vulnerable'],
            ":board" => $board,
            ":deal_n" => $hand['deal']['N'],
            ":deal_e" => $hand['deal']['E'],
            ":deal_s" => $hand['deal']['S'],
            ":deal_w" => $hand['deal']['W'],
            ":optimum_score" => $hand['optimumScore'],
            ":optimum_contract" => $hand['optimumContract'],
            ":dealer" => $hand['dealer']
        ];

        $response = $handQ->execute($handArray);

        if ($response === false)
            throw new PDOException("Error al insertar la Mano del board $board");

        return $response;
    }


    public function deleteHands(int $tournament_id)
    {
        $query = "DELETE FROM hands WHERE tournament_id = :tournament_id";
        $handQ = $this->pdoConnection->prepare($query);
        $response = $handQ->execute([':tournament_id' => $tournament_id]);
        if ($response === false)
            throw new PDOException("Error al eliminar las manos ya existentes.");

        return $response;
    }
}
