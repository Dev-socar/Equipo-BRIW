<?php
require_once("admin/config.php");
include("helpers/federatedSearch.php");

$queryWords = "";
$allResults = [];
isset($_GET["search"]) ? $queryWords = $_GET["search"] : true;

if( $queryWords != "" ){

    $europeanaResults = [];
    $plosResults = [];
    $normalizeEuropeanaResults = [];
    $normalizePlosResults = [];

    $expandedQueries  = expandMultipleTerms($queryWords);

    $originalResults = searchEuropeana($queryWords);
    if ($originalResults) {
        $europeanaResults = array_merge($europeanaResults, $originalResults);
    }

    $originalResultsPLOS = searchPLOS($queryWords);
    if ($originalResultsPLOS) {
        $plosResults = array_merge($plosResults, $originalResultsPLOS);
    }

    foreach ($expandedQueries as $term => $expandedTerms) {
        foreach ($expandedTerms as $expandedTerm) {
            $expandedResultsEuropeana = searchEuropeana($expandedTerm);
            if ($expandedResultsEuropeana) {
                $europeanaResults = array_merge($europeanaResults, $expandedResultsEuropeana);
            }
            
            $expandedResultsPLOS = searchPLOS($expandedTerm);
            if ($expandedResultsPLOS) {
                $plosResults = array_merge($plosResults, $expandedResultsPLOS);
            }
        }
    }

    if (!empty($europeanaResults)) {
        $normalizeEuropeanaResults = normalizeScores($europeanaResults);
    }

    if (!empty($plosResults)) {
        $normalizePlosResults = normalizeScores($plosResults);
    }

    $allResults = array_merge($normalizeEuropeanaResults, $normalizePlosResults);

    usort($allResults, function ($a, $b) {
        return $b['normalizeScore'] <=> $a['normalizeScore']; // Ordenar de mayor a menor
    });

}







require("views/index.view.php");
