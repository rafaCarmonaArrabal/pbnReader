<?php


namespace App\Controllers;


use App\Classes\PbnForm;
use PDOException;

class ScorecardRepository extends PbnForm
{
    public function getScorecard(int $tournament_id, int $pair_id)
    {

        // get direction.
        $queryDirection = "SELECT direction from rankings WHERE tournament_id = :tournament_id AND pair_id = :pair_id";
        $directionQ = $this->pdoConnection->prepare($queryDirection);
        $response = $directionQ->execute([':tournament_id' => $tournament_id, ':pair_id' => $pair_id]);

        if ($response === false)
            throw new PDOException("Error al recuperar los scorecard.");
        $queryScorecard = $directionQ->fetchColumn() == 'N-S' ?
                "SELECT (SELECT pair_id FROM rankings WHERE id = travellers.pairNS_id) as pair_id, (SELECT pair_id FROM rankings WHERE id = travellers.pairEW_id) as rival_id, travellers.board, travellers.declarer, travellers.lead, travellers.result, travellers.mp_ns as mp, travellers.score_ns as score, travellers.percentage_ns as percentage, (SELECT names FROM rankings WHERE id = travellers.pairEW_id) as rival, rankings.names FROM travellers, rankings WHERE rankings.tournament_id = :tournament_id AND rankings.id = travellers.pairNS_id AND rankings.pair_id = :pair_id"
            :   "SELECT (SELECT pair_id FROM rankings WHERE id = travellers.pairEW_id) as pair_id, (SELECT pair_id FROM rankings WHERE id = travellers.pairNS_id) as rival_id, travellers.board, travellers.declarer, travellers.lead, travellers.result, travellers.mp_ew as mp, travellers.score_ew as score, travellers.percentage_ew as percentage, (SELECT names FROM rankings WHERE id = travellers.pairNS_id) as rival, rankings.names FROM travellers, rankings WHERE rankings.tournament_id = :tournament_id AND rankings.id = travellers.pairEW_id AND rankings.pair_id = :pair_id";
        $scoreCardQ = $this->pdoConnection->prepare($queryScorecard);
        $response = $scoreCardQ->execute([':tournament_id' => $tournament_id, ':pair_id' => $pair_id]);
        $scorecards = $scoreCardQ->fetchAll(\PDO::FETCH_ASSOC);

        if ($response === false)
            throw new PDOException("Error al recoger los scorecard.");
        return $scorecards;
    }
}
