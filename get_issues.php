<?php 

$repo_link = json_decode(file_get_contents("php://input"), true);
$repo_link = $repo_link['data'];

//array to store the final counts
$response = array();

//for Curl call
function call_curl($url, $agent){
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL,$url);
    curl_setopt($ch, CURLOPT_USERAGENT, "geekyprincess");
    curl_setopt($ch, CURLOPT_HTTPHEADER, array( 'Accept: application/json'));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $result=curl_exec($ch);
    curl_close($ch);
    return $result;
}


//decode the jsonp into array
function jsonp_decode($result) { 
    if($result[0] !== '[' && $result[0] !== '{') { 
       $result = substr($result, strpos($result, '('));
    }
    return json_decode(trim($result,'();'), true);
}


//to get the last page count(pagination) in issues
function getPageCount($url){
    
   $result = call_curl($url, 'ashima');
    $data = jsonp_decode($result);
    
    // if there are no issues for the repo return 0
    if(empty($data['data'])){
        $response['total_count'] = 0;
        $response['last_24_count'] = 0;
        $response['last_7_count'] = 0;
        $response['before_7'] = 0;
        die(json_encode($response));
    }
    
    //when there is only one page 
    else if(empty ($data['meta']['Link'])){
        return 1; 
    }
    
    //get the count of the last page 
    else{
       $link = $data['meta']['Link'][1][0];
        $page= (explode('&page=', $link, 2));
        $page= (explode('&', $page[1], 2));
        $page_count = $page[0];
        return $page_count; 
    }

}

//extract the :org/:repo link
$links = (explode('https://github.com/', $repo_link, 2));
$repo_name_link = rtrim($links[1],"/");     


$i = 1;
$all_issues = array();
$max_page_count = 1;

//get the issues for the API 
while($i<($max_page_count+1)){
    $url = 'https://api.github.com/repos/'.$repo_name_link.'/issues?q=state:open&page='.$i.'&per_page=25&callback=foo&';
    if($i == '1'){
        $max_page_count = getPageCount($url);
    }
    $result = call_curl($url, 'ashima');
    $new = jsonp_decode($result);
    $all_issues = array_merge($all_issues, $new['data']);
    $i++;
}

//Counters to save the count for the reqd table entries
$total_count = 0 ;
$last_24_count = 0;
$last_7_count = 0;
$before_7 = 0;

//get 24hours back time and 7 days ago time
$time_7 = strtotime('7 days ago');
$time_24 = strtotime('-1 day', time());

//update the counters 
foreach($all_issues as $issue){
    if(!array_key_exists('pull_request', $issue)){
        $total_count++; 
        $issue_time = strtotime($issue['created_at']);
        if($issue_time > $time_24){
            $last_24_count++;
        }
        else if($issue_time > $time_7){
            $last_7_count++;
        }
        else{
            $before_7++;
        }
        
    }
}

//saving all the counters in 'response' 
$response['total_count'] = $total_count;
$response['last_24_count'] = $last_24_count;
$response['last_7_count'] = $last_7_count;
$response['before_7'] = $before_7;

$val = json_encode($response);
echo($val);