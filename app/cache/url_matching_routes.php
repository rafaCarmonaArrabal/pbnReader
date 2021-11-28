<?php

/**
 * This file has been auto-generated
 * by the Symfony Routing Component.
 */

return [
    false, // $matchHost
    [ // $staticRoutes
        '/' => [[['_route' => 'home', '_file' => 'HomeController.php', '_method' => 'index'], null, null, null, false, false, null]],
        '/pbn' => [[['_route' => 'pbn', '_file' => 'ReadPbnController.php', '_method' => 'index'], null, null, null, false, false, null]],
        '/post/pbn' => [[['_route' => 'post_pbn', '_file' => 'ReadPbnController.php', '_method' => 'post'], null, null, null, false, false, null]],
        '/resultados' => [[['_route' => 'results', '_file' => 'ResultController.php', '_method' => 'index'], null, null, null, false, false, null]],
    ],
    [ // $regexpList
        0 => '{^(?'
                .'|/resultado/(?'
                    .'|(\\d+)(*:26)'
                    .'|(\\d+)/scorecard/(\\d*)(*:54)'
                    .'|(\\d+)/scorecard(*:76)'
                .')'
            .')/?$}sD',
    ],
    [ // $dynamicRoutes
        26 => [[['_route' => 'result', '_file' => 'ResultController.php', '_method' => 'show', '_args' => ['id']], ['id'], null, null, false, true, null]],
        54 => [[['_route' => 'scorecard', '_locale' => 0, '_file' => 'ScorecardController.php', '_method' => 'show', '_args' => ['tournament_id', 'pair_id']], ['tournament_id', 'pair_id'], null, null, false, true, null]],
        76 => [
            [['_route' => 'scorecard', '_locale' => 1, '_file' => 'ScorecardController.php', '_method' => 'show', '_args' => ['tournament_id', 'pair_id']], ['tournament_id'], null, null, false, false, null],
            [null, null, null, null, false, false, 0],
        ],
    ],
    null, // $checkCondition
];
