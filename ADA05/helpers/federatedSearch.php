<?php



function expandMultipleTerms($query) {
    $terms = explode(' ', $query);

    $stopWords = stopWords();
    
    $expandedQueries = [];
    
    foreach ($terms as $term) {
        // Salta las stopWords
        if (in_array(strtolower($term), $stopWords)) {
            continue;
        }
        
        $expandedTerms = expandQuery($term);
        $expandedQueries[$term] = $expandedTerms;
    }
    
    return $expandedQueries;
}



function expandQuery($word) {
    $url = "https://api.datamuse.com/words?ml=" . urlencode($word); 
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);
    
    // Decodificar la respuesta JSON
    $words = json_decode($response, true);
    
 
    $expandedTerms = [];
    foreach ($words as $wordData) {
        $expandedTerms[] = $wordData['word'];
        if (count($expandedTerms) >= 2) {
            break; 
        }
    }
    
    return $expandedTerms;
}


function stopWords(){
    return [ 
        'a', 'al', 'algo', 'algunas', 'algunos', 'ante', 'antes', 'como', 'con', 
        'contra', 'cual', 'cuando', 'de', 'del', 'desde', 'donde', 'durante', 'e', 
        'el', 'ella', 'ellas', 'ellos', 'en', 'entre', 'era', 'erais', 'eran', 
        'eras', 'eres', 'es', 'esa', 'esas', 'ese', 'eso', 'esos', 'esta', 'estaba', 
        'estado', 'estais', 'estamos', 'estan', 'estar', 'estará', 'estas', 'este', 
        'esto', 'estos', 'estoy', 'fue', 'fueron', 'fui', 'fuimos', 'ha', 'había', 
        'habían', 'habiendo', 'habremos', 'habrá', 'hace', 'haces', 'hacen', 'hacer', 
        'hacia', 'hasta', 'hay', 'la', 'las', 'lo', 'los', 'me', 'mi', 'mis', 'mientras', 
        'muy', 'nos', 'nosotros', 'nuestra', 'nuestro', 'o', 'os', 'otra', 'otros', 'para', 
        'pero', 'por', 'porque', 'que', 'qué', 'quién', 'quienes', 'se', 'ser', 'si', 'sí', 
        'sin', 'sobre', 'sois', 'son', 'su', 'sus', 'te', 'tengo', 'tendrá', 'tendrás', 
        'tendrán', 'tengo', 'tendrá', 'tendremos', 'tu', 'tus', 'un', 'una', 'uno', 'unos', 
        'vos', 'vosotros', 'vuestro', 'y', 'ya',  'a', 'about', 'above', 'after', 'again', 'against', 'all', 'am', 'an', 'and', 'any', 
        'are', 'aren\'t', 'as', 'at', 'be', 'because', 'been', 'before', 'being', 'below', 
        'between', 'both', 'but', 'by', 'can\'t', 'cannot', 'could', 'couldn\'t', 'did', 
        'didn\'t', 'do', 'does', 'doesn\'t', 'doing', 'don\'t', 'down', 'during', 'each', 
        'few', 'for', 'from', 'further', 'had', 'hadn\'t', 'has', 'hasn\'t', 'have', 'haven\'t', 
        'having', 'he', 'he\'d', 'he\'ll', 'he\'s', 'her', 'here', 'here\'s', 'hers', 'herself', 
        'him', 'himself', 'his', 'how', 'how\'s', 'i', 'i\'d', 'i\'ll', 'i\'m', 'i\'ve', 'if', 
        'in', 'into', 'is', 'isn\'t', 'it', 'it\'s', 'its', 'itself', 'let\'s', 'me', 'more', 
        'most', 'mustn\'t', 'my', 'myself', 'no', 'nor', 'not', 'of', 'off', 'on', 'once', 
        'only', 'or', 'other', 'ought', 'our', 'ours', 'ourselves', 'out', 'over', 'own', 
        'same', 'shan\'t', 'she', 'she\'d', 'she\'ll', 'she\'s', 'should', 'shouldn\'t', 
        'so', 'some', 'such', 'than', 'that', 'that\'s', 'the', 'their', 'theirs', 'them', 
        'themselves', 'then', 'there', 'there\'s', 'these', 'they', 'they\'d', 'they\'ll', 
        'they\'re', 'they\'ve', 'this', 'those', 'through', 'to', 'too', 'under', 'until', 
        'up', 'very', 'was', 'wasn\'t', 'we', 'we\'d', 'we\'ll', 'we\'re', 'we\'ve', 'were', 
        'weren\'t', 'what', 'what\'s', 'when', 'when\'s', 'where', 'where\'s', 'which', 'while', 
        'who', 'who\'s', 'whom', 'why', 'why\'s', 'with', 'won\'t', 'would', 'wouldn\'t', 'you', 
        'you\'d', 'you\'ll', 'you\'re', 'you\'ve', 'your', 'yours', 'yourself', 'yourselves'
    ];

}


function searchEuropeana($query, $rows = 10, $start = 1) {
    $apiKey = 'rekenting'; 
    $url = "https://api.europeana.eu/record/v2/search.json?wskey=$apiKey&query=" . urlencode($query);
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    
    $response = curl_exec($ch);
    curl_close($ch);
    
    $result = json_decode($response, true);

    if (isset($result['items']) && count($result['items']) > 0) {
        // Crear la nueva estructura de resultados
        $busqueda = [];
        foreach ($result['items'] as $item) {
            $busqueda[] = [
                "titulo" => $item['title'][0] ?? 'Sin título', 
                "link" => 'https://www.europeana.eu/item/' . $item['id'],              
                "score" => $item['score'] ?? 0,                
                "normalizeScore" => null,
                "buscador" => "Europeana"                  
            ];
        }
        return $busqueda;
    } else {
        return null; 
    }
}

function normalizeScores($results) {
    // Obtener todos los valores de score
    $scores = array_map(function($busqueda) {
        return $busqueda['score'];
    }, $results);

    // Encontrar los valores mínimo y máximo de score
    $minScore = min($scores);
    $maxScore = max($scores);

    // Normalizar los valores de score
    foreach ($results as &$busqueda) {
        if ($maxScore - $minScore == 0) {
            // Si todos los scores son iguales, establecer normalizeScore en 1
            $busqueda['normalizeScore'] = 1;
        } else {
            $busqueda['normalizeScore'] = ($busqueda['score'] - $minScore) / ($maxScore - $minScore);
        }
    }
    return $results;
}


function searchPLOS($query, $rows = 10) {
    $url = "https://api.plos.org/search?q=" . urlencode($query) . "&rows=$rows&wt=json";
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    
    $response = curl_exec($ch);
    curl_close($ch);
    
    $result = json_decode($response, true);
    
    if (isset($result['response']['docs']) && count($result['response']['docs']) > 0) {
        $busqueda = [];
        foreach ($result['response']['docs'] as $item) {
            $busqueda[] = [
                "titulo" => $item['title_display'] ?? 'Sin título',
                "link" => "https://journals.plos.org/plosone/article?id=" . $item['id'],
                "score" => $item['score'] ?? 0,
                "normalizeScore" => null,
                "buscador" => "PLOS" 
            ];
        }
        return $busqueda;
    } else {
        return null; 
    }
}


?> 