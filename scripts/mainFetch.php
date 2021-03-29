<?php

    date_default_timezone_set('UTC');

    // Connect
    $user='sysadm';
    $pass='gtf0mate';
    try { 
        $conn = new PDO('sqlite:../db/central', $user, $pass);
    }
    catch (Exception $e) {
        die("Unable to connect: ".$e->getMessage());
    }

    function prepare($data) {
        // Remove all saboteurs from data
        $predone = preg_replace("/[\[\]\*\{\}\(\)\+\|\^\&\.\\/?<>'\"]/s", "", $data);
        // Remove whitespaces
        $done = preg_replace("/\s{2,}/s", " ", $predone);
        
        // Explode and return data for further processing
        return explode("!", trim($done));
    }

    try {
        if(isset($_GET["data"])) $data = prepare($_GET["data"]);
        else throw new Exception("There's no data to process.");
    }
    catch (Exception $e) {
        print_r($e->getMessage());
    }

    // Data formatting: [0] - search type (0 - simple, 1 - complex), [1] - target table, [2] - WHERE arg1, [3] - WHERE arg2, [4] - (OPTIONAL) target columns
    // Additional info: 
    //      if [2] is set to 0, override [2] and [3] by pulling all rows from database
    //      if [4] is set to 0 or unset, override [4] by pulling all columns from database
    if ($data[4]==0 || $data[4]==null) $data[4]="*";
    if ($data[0]==0) {
        try {

            // Bind columns
            $select = "SELECT ".$data[4];
        
            // Bind table
            switch($data[1]) {
                // 0 -> Map.
                case 0: $select=$select." FROM maps"; break;
                // 1 -> God.
                case 1: $select=$select." FROM gods"; break;
                // 2 -> Language.
                case 2: $select=$select." FROM languages"; break;
                // 3 -> Culture.
                case 3: $select=$select." FROM cultures"; break;
                // 4 -> Architecture style.
                case 4: $select=$select." FROM architectures"; break;
                // 5 -> Race.
                case 5: $select=$select." FROM races"; break;
                // 6 -> Perk.
                case 6: $select=$select." FROM perks"; break;
                // 7 -> Land.
                case 7: $select=$select." FROM lands"; break;
                // 8 -> Event.
                case 8: $select=$select." FROM events"; break;
                // 9 -> Kingdom.
                case 9: $select=$select." FROM kingdoms"; break;
                // 10 -> Person.
                case 10: $select=$select." FROM people"; break;
                // If [0] not correct, throw exception
                default: throw new Exception("Given ID is wrong");
            }

            // Bind (if required) WHERE arguments
            if ($data[2]!=0) {
                $select=$select." WHERE ".$data[2];
                $select=$select." = ".$data[3];
            }

            if(!($stmt = $conn->prepare($select))) throw new Exception("Error on preparation of statement.");

            // Execute statement
            $stmt->execute();

            // Return data
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            print_r($results);
        }
        catch (Exception $e) {
            print_r($e->getMessage());
        }
    }
    else if ($data==1) {

    }
    else print_r("Wrong query mode.");
?>