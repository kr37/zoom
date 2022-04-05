<!DOCTYPE html>
<html>
<head>
    <style>
        table   {border-collapse:collapse;}
        th,td   {padding: 0 .4em; border: 1px gray solid;}
        .webinar {background-color: papayawhip;}
        .meeting {background-color: pink;}
    </style>
</head>
<body>

<?php
require_once './Zoom/Index.php';
use Zoom\Meeting;
use Zoom\Webinar;

function datefmt($datestr){
    $time = strtotime($datestr);
    if ($time < 1577833200) { // Prior to 2020 (bogus date)
        return '';
    } else {
        return date('D,&\n\b\s\p;M&\n\b\s\pj,&\n\b\s\p'."'y g:i a",strtotime($datestr)-3600*8);
    }
}

echo <<<TABLEHEAD
<table class="webinar">
    <thead>
        <tr><th colspan='4' style='text-align:center;'>Webinars</th></tr>
        <tr><th>id</th><th>Topic</th><th>Start</th><th>Participants</th><th>Link</th></tr>
    </thead>
    <tbody>
TABLEHEAD;

    
// ---------------WEBINARS----------------------
$webinar = new Webinar();
//$userId = $webinar->getUserId(); echo "userId: $userId<br>\n";

$i = 0;
$list = $webinar->list(['page_size' => '300', 'to' => date('Y-m-d')]);
foreach (array_reverse($list['webinars']) as $m) {
    $i++;
    $instances = $webinar->instances($m['id']);
    $partiers = niceParticipants($webinar->listParticipants($m['id']));
    //$export .= "<br>\n";
    //foreach ($instances['webinars'] as $inst)
    //    $export .= "$inst[occurrence_id] ".datefmt($inst['start_time'])." $inst[uuid]<br>";
    $start = array_key_exists('start_time', $m) ? datefmt($m['start_time']) : '';
    $link = "<a href='$m[join_url]' target='_blank'>Start or join</a>";
    echo "<tr><td>$i</td><td>$m[topic]</td><td>$start</td><td>$partiers</td><td>$link</td></tr>\n";
}
echo "    </tbody>\n</table>\n";

// ---------------MEETINGS----------------------
echo <<<TABLE
<br>
<table class="meeting">
    <thead>
        <tr><th colspan='4' style='text-align:center;'>Meetings</th></tr>
        <tr><th>id</th><th>Topic</th><th>Start</th><th>Zoom Link</th><th>More data</th></tr>
    </thead>
    <tbody>
TABLE;
$meeting = new Meeting();
$list = $meeting->list();
foreach ($list['meetings'] as $m) {
    $partiers = niceParticipants($meeting->listParticipants($m['id']));
    $start = array_key_exists('start_time', $m) ? datefmt($m['start_time']) : '';
    $link = "<a href='$m[join_url]' target='_blank'>Start or join</a>";
    echo "<tr><td>$m[id]</td><td>$m[topic]</td><td>$start</td><td>$link</td><td>$partiers</td></tr>\n";
}

echo "    </tbody>\n</table>\n";

function niceParticipants($partiers) {
    if ($partiers !== false) {
        $participants = $partiers['participants'];
        $partsUnique = unique_multidim_array($participants, 'name');
        array_multisort(array_column($participants, 'name'), SORT_ASC, $participants);
    } else {
        $parts = [];
    }
    $count = count($partsUnique);
    $export = "<b>$count</b> unique. <b>All</b>:  ";
    foreach ($participants as $p)
        $export .= "$p[name], ";
    //$export .= var_export($participants, true);
    
    return substr($export,0,-2);
}


function unique_multidim_array($array, $key) {
    $temp_array = array();
    $i = 0;
    $key_array = array();
   
    foreach($array as $val) {
        if (!in_array($val[$key], $key_array)) {
            $key_array[$i] = $val[$key];
            $temp_array[$i] = $val;
        }
        $i++;
    }
    return $temp_array;
}

?>
</body>
</html>
