<?php


namespace App\Controllers;

use App\Controllers\ScorecardRepository;

class ScorecardController
{
    private const SHOW_VIEW = VIEW_PATH . '/scorecard/showView.php';

    /**
     * @param int $tournament_id
     * @param int|null $pair_id
     * @return view
     */
    public function show(int $tournament_id, int $pair_id = null)
    {
        $scorecardR = new ScorecardRepository();
        $scorecards = $scorecardR->getScorecard($tournament_id, $pair_id);
        return include self::SHOW_VIEW;
    }
}
