<?php

namespace App\Controllers;

use App\Repositories\TournamentRepository;

class ResultController
{
    private const VIEW = VIEW_PATH . '/result/indexView.php';
    private const RANKING_VIEW = VIEW_PATH . '/result/rankingView.php';

    /**
     * @return mixed
     */
    public function index()
    {
        $tournamentR = new TournamentRepository();
        $tournaments = $tournamentR->getTournaments();
        return include self::VIEW;
    }

    /**
     * @param $id
     * @return mixed
     */
    public function show($tournament_id)
    {
        $rankingR = new RankingsRepository();
        // get ranking by tournament_id
        $rankings = $rankingR->getRanking($tournament_id);
        $tournamentR = new TournamentRepository();
        $tournament = $tournamentR->getTournaments($tournament_id);
        $tournamentName = $tournament[0]['name'];
        return include self::RANKING_VIEW;
    }
}

