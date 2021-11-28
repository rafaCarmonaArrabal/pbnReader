<?php

namespace App\Repositories;

use App\Classes\PbnForm;
use PDOException;

class TournamentRepository extends PbnForm
{
    /**
     * @param int|null $id
     * @return array
     */
    public function getTournaments(int $id = null)
    {
        $query = isset($id) ? "SELECT * FROM tournaments WHERE id = :id" : "SELECT * FROM tournaments";
        $tournamentQ = $this->pdoConnection->prepare($query);
        $response = isset($id) ? $tournamentQ->execute([':id' => $id]) : $tournamentQ->execute();
        $tournaments = $tournamentQ->fetchAll(\PDO::FETCH_ASSOC);

        if ($response === false)
            throw new PDOException("Error al recoger los resultados.");

        return $tournaments;
    }

    public function getPlayers(int $tournament_id){
        $query = "SELECT names, pair_id from rankings WHERE tournament_id = :tournament_id";
        $namesQ = $this->pdoConnection->prepare($query);
        $response = $namesQ->execute([':tournament_id' => $tournament_id]);

        if ($response === false)
            throw new PDOException("Error al recuperar los scorecard.");
    }
}
