<?php

namespace App\Controllers;


use Exception;
use HttpInvalidParamException;
use App\Classes\PbnForm;
use App\Classes\PDO;
use PDOException;

class RankingsRepository extends PbnForm
{

    /**
     * @param \SplFileObject $file
     * @param int $seekNumber
     * @param int $torneo
     * @return array
     */
    public function getRankings(\SplFileObject $file, int $seekNumber, int $torneo)
    {
        // Now we set the current line in the first line of the rankings.
        $file->seek($seekNumber);
        // base of the array
        $rankings = [];
        do {
            $names = [];
            $line = utf8_encode($file->current());
            // Sacamos los nombres.
            \preg_match('/([A-zÁ-ú0-9\s-]+)[\s][-][\s]([A-zÁ-ú0-9\s-]+)/', $line, $names);
            // quitamos comillas y eliminamos espacios, comillas, y también el nombre del array.
            $line = $this->formatter->stringToArray($line, true, [$names[0], '"']);

            $rankings [] =
                [
                    ":rank" => $line[0],
                    ":torneo_id" => $torneo,
                    ":pairId" => $line[1],
                    ":section" => $line[2],
                    ":table" => $line[3],
                    ":direction" => $line[4],
                    ":totalScoreMp" => $line[5],
                    ":totalPercentage" => $line[6],
                    ":nrBoards" => $line[7],
                    ":names" => $names[0],
                    ":memberId1" => $line[8],
                    ":memberId2" => $line[9]
                ];
            $file->next();
        } while (\strpos($file->current(), self::SCORE_TABLE) === false);
        $this->scoreTseekNum = $file->key();
        return $rankings;
    }

    /**
     * @return int
     */
    public function getScoreTseekNum()
    {
        return $this->scoreTseekNum ?? 0;
    }

    public function insertRankings($rankings)
    {
        $queryRankings = "INSERT INTO rankings (rank, tournament_id, pair_id, `section`, tabla, direction, total_score_mp, 
                        percentage_session, nr_boards, `names`, member_1_id, member_2_id)
            VALUES (:rank, :torneo_id, :pairId, :section, :table, :direction, :totalScoreMp, :totalPercentage, :nrBoards,  :names, :memberId1, :memberId2)";

        $rankingsQ = $this->pdoConnection->prepare($queryRankings);
        foreach ($rankings as $ranking) {
            $response = $rankingsQ->execute($ranking);
            if ($response === false) {
                throw new PDOException("No se pudieron insertar el resultado con el result número: {$ranking[':rank']}");
            }
        }
        return $rankingsQ;
    }

    public function deleteRankings(int $tournament_id){
        $query = "DELETE FROM rankings WHERE tournament_id = :tournament_id";
        $handQ = $this->pdoConnection->prepare($query);
        $response = $handQ->execute([':tournament_id' => $tournament_id]);
        if ($response === false)
            throw new PDOException("Error al intentar eliminar los resultados ya existentes.");

        return $response;
    }

    /**
     * @param int $torneo
     * @return array
     */
    public function getRanking(int $torneo){
        $query = "SELECT * FROM rankings WHERE tournament_id = :id";
        $tournamentQ = $this->pdoConnection->prepare($query);
        $response = $tournamentQ->execute([':id' => $torneo]);
        $tournaments = $tournamentQ->fetchAll(\PDO::FETCH_ASSOC);
        if ($response === false)
            throw new PDOException("Error al recoger los resultados.");

        return $tournaments;
    }

}

