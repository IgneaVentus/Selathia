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

    // Prepare data for processing
    function prepare($data) {
        // Remove all saboteurs from data
        $predone = preg_replace("/[\[\]\*\{\}\(\)\+\|\^\&\.\\\?<>'\"]/s", "", $data);
        // Remove whitespaces
        $done = preg_replace("/\s{2,}/s", " ", $predone);
        
        // Explode and return data for further processing
        return explode("!", trim($done));
    }

    // New description pair insertion
    function insertDesc ($short, $long) {
        global $conn;

        // Prepare statement
        $descInsert = "INSERT INTO descriptions (shortdesc, longdesc) VALUES (?,?)";
        $stmt = $conn->prepare($descInsert);

        // Bind values
        if (!$stmt->bindValue(1, $short, PDO::PARAM_STR)) throw new Exception ("Error during description insert on first bind");
        if (!$stmt->bindValue(2, $long, PDO::PARAM_STR)) throw new Exception ("Error during description insert on second bind");

        // Insert data
        if (!$stmt->execute()) throw new Exception ("Error during description insert on execution");
        
        // Return ID of inserted row
        return ($conn->lastInsertId());
    }

    // New map location insertion
    function insertMapLoc ($posX,$posY,$posZ,$map_id) {
        global $conn;

        // Prepare statement
        $insert = "INSERT INTO mapLocations (posX, posY, posZ, map_id) VALUES (?,?,?,?)";
        $stmt = $conn->prepare($insert);

        // Bind values
        if (!$stmt->bindValue(1, $posX, PDO::PARAM_INT)) throw new Exception ("Error during location insert on first bind");
        if (!$stmt->bindValue(2, $posY, PDO::PARAM_INT)) throw new Exception ("Error during location insert on second bind");
        if (!$stmt->bindValue(3, $posZ, PDO::PARAM_INT)) throw new Exception ("Error during location insert on third bind");
        if (!$stmt->bindValue(4, $map_id, PDO::PARAM_INT)) throw new Exception ("Error during location insert on fourth bind");

        // Execute
        if (!$stmt->execute()) throw new Exception ("Error during location insert on execution");

        // Return ID of inserted row
        return ($conn->lastInsertId());
    }

    // New map insertion
    function insertMap ($name, $path, $height, $width) {
        global $conn;

        // Prepare statement
        $insert = "INSERT INTO maps (name, path, height, width) VALUES (?,?,?,?)";
        $stmt = $conn->prepare($insert);

        // Bind values
        if(!$stmt->bindValue(1, $name, PDO::PARAM_STR)) throw new Exception("Error during inserting map on first bind.");
        if(!$stmt->bindValue(2, $path, PDO::PARAM_STR)) throw new Exception("Error during inserting map on second bind.");
        if(!$stmt->bindValue(3, $height, PDO::PARAM_INT)) throw new Exception("Error during inserting map on third bind.");
        if(!$stmt->bindValue(4, $width, PDO::PARAM_INT)) throw new Exception("Error during inserting map on fourth bind.");

        // Execute statement
        if(!$stmt->execute()) throw new Exception("Error during inserting map on execution");

        // Return ID of inserted row
        return ($conn->lastInsertId());
    }

    // New god insertion
    function insertGod ($name, $domain, $desc_id) {
        global $conn;

        // Prepare statement
        $insert = "INSERT INTO gods (name, domain, desc_id) VALUES (?,?,?)";
        $stmt = $conn->prepare($insert);

        // Bind values
        if(!$stmt->bindValue(1, $name, PDO::PARAM_STR)) throw new Exception("Error during inserting god on first bind.");
        if(!$stmt->bindValue(2, $domain, PDO::PARAM_STR)) throw new Exception("Error during inserting god on second bind.");
        if(!$stmt->bindValue(3, $desc_id, PDO::PARAM_INT)) throw new Exception("Error during inserting god on third bind.");

        // Execute statement
        if(!$stmt->execute()) throw new Exception("Error during inserting god on execution");

        // Return ID of inserted row
        return ($conn->lastInsertId());
    }

    // New language insertion
    function insertLang ($name, $desc) {
        global $conn;

        // Prepare statement
        $insert = "INSERT INTO languages (name, desc) VALUES (?,?)";
        $stmt = $conn->prepare($insert);

        // Bind values
        if(!$stmt->bindValue(1, $name, PDO::PARAM_STR)) throw new Exception("Error during inserting language on first bind.");
        if(!$stmt->bindValue(2, $desc, PDO::PARAM_STR)) throw new Exception("Error during inserting language on second bind.");

        // Execute statement
        if(!$stmt->execute()) throw new Exception("Error during inserting language on execution");

        // Return ID of inserted row
        return ($conn->lastInsertId());
    }

    // New culture insertion
    function insertCulture ($name, $desc_id) {
        global $conn;

        // Prepare statement
        $insert = "INSERT INTO cultures (name, desc_id) VALUES (?,?)";
        $stmt = $conn->prepare($insert);

        // Bind values
        if(!$stmt->bindValue(1, $name, PDO::PARAM_STR)) throw new Exception("Error during inserting culture on first bind.");
        if(!$stmt->bindValue(2, $desc_id, PDO::PARAM_INT)) throw new Exception("Error during inserting culture on second bind.");

        // Execute statement
        if(!$stmt->execute()) throw new Exception("Error during inserting culture on execution");

        // Return ID of inserted row
        return ($conn->lastInsertId());
    }

    // New architecture style insertion
    function insertArchitecture ($name, $desc_id) {
        global $conn;

        // Prepare statement
        $insert = "INSERT INTO architectures (name, desc_id) VALUES (?,?)";
        $stmt = $conn->prepare($insert);

        // Bind values
        if(!$stmt->bindValue(1, $name, PDO::PARAM_STR)) throw new Exception("Error during inserting architecture on first bind.");
        if(!$stmt->bindValue(2, $desc_id, PDO::PARAM_INT)) throw new Exception("Error during inserting architecture on second bind.");

        // Execute statement
        if(!$stmt->execute()) throw new Exception("Error during inserting architecture on execution");

        // Return ID of inserted row
        return ($conn->lastInsertId());
    }

    // New race insertion
    function insertRace ($name, $desc_id) {
        global $conn;

        // Prepare statement
        $insert = "INSERT INTO races (name, desc_id) VALUES (?,?)";
        $stmt = $conn->prepare($insert);

        // Bind values
        if(!$stmt->bindValue(1, $name, PDO::PARAM_STR)) throw new Exception("Error during inserting race on first bind.");
        if(!$stmt->bindValue(2, $desc_id, PDO::PARAM_INT)) throw new Exception("Error during inserting race on second bind.");

        // Execute statement
        if(!$stmt->execute()) throw new Exception("Error during inserting race on execution");

        // Return ID of inserted row
        return ($conn->lastInsertId());
    }

    // New perk insertion
    function insertPerk ($name, $desc, $is_positive) {
        global $conn;

        // Prepare statement
        $insert = "INSERT INTO perks (name, desc, is_positive) VALUES (?,?,?)";
        $stmt = $conn->prepare($insert);

        // Bind values
        if(!$stmt->bindValue(1, $name, PDO::PARAM_STR)) throw new Exception("Error during inserting perk on first bind.");
        if(!$stmt->bindValue(2, $desc, PDO::PARAM_STR)) throw new Exception("Error during inserting perk on second bind.");
        if(!$stmt->bindValue(3, $is_positive, PDO::PARAM_BOOL)) throw new Exception("Error during inserting perk on third bind.");

        // Execute statement
        if(!$stmt->execute()) throw new Exception("Error during inserting perk on execution");

        // Return ID of inserted row
        return ($conn->lastInsertId());
    }

    // New land insertion
    function insertLand ($name, $path, $desc_id) {
        global $conn;

        // Prepare statement
        $insert = "INSERT INTO lands (name, path, desc_id) VALUES (?,?,?)";
        $stmt = $conn->prepare($insert);

        // Bind values
        if(!$stmt->bindValue(1, $name, PDO::PARAM_STR)) throw new Exception("Error during inserting land on first bind.");
        if(!$stmt->bindValue(2, $path, PDO::PARAM_STR)) throw new Exception("Error during inserting land on second bind.");
        if(!$stmt->bindValue(3, $desc_id, PDO::PARAM_INT)) throw new Exception("Error during inserting land on third bind.");

        // Execute statement
        if(!$stmt->execute()) throw new Exception("Error during inserting land on execution");

        // Return ID of inserted row
        return ($conn->lastInsertId());
    }

    // New event insertion
    function insertEvent ($name, $location_id, $desc_id) {
        global $conn;

        // Prepare statement
        $insert = "INSERT INTO events (name, location_id, desc_id) VALUES (?,?,?)";
        $stmt = $conn->prepare($insert);

        // Bind values
        if(!$stmt->bindValue(1, $name, PDO::PARAM_STR)) throw new Exception("Error during inserting event on first bind.");
        if(!$stmt->bindValue(2, $location_id, PDO::PARAM_INT)) throw new Exception("Error during inserting event on first bind.");
        if(!$stmt->bindValue(3, $desc_id, PDO::PARAM_INT)) throw new Exception("Error during inserting event on first bind.");

        // Execute statement
        if(!$stmt->execute()) throw new Exception("Error during inserting event on execution");

        // Return ID of inserted row
        return ($conn->lastInsertId());
    }

    // New kingdom insertion
    function insertKingdom ($name, $ruler, $type, $desc_id, $culture_id) {
        global $conn;

        // Prepare statement
        $insert = "INSERT INTO kingdoms (name, ruler, type, desc_id, culture_id) VALUES (?,?,?,?,?)";
        $stmt = $conn->prepare($insert);

        // Bind values
        if(!$stmt->bindValue(1, $name, PDO::PARAM_STR)) throw new Exception("Error during inserting kingdom on first bind.");
        if(!$stmt->bindValue(2, $ruler, PDO::PARAM_STR)) throw new Exception("Error during inserting kingdom on second bind.");
        if(!$stmt->bindValue(3, $type, PDO::PARAM_STR)) throw new Exception("Error during inserting kingdom on third bind.");
        if(!$stmt->bindValue(4, $desc_id, PDO::PARAM_INT)) throw new Exception("Error during inserting kingdom on fourth bind.");
        if(!$stmt->bindValue(5, $culture_id, PDO::PARAM_INT)) throw new Exception("Error during inserting kingdom on fifth bind.");

        // Execute statement
        if(!$stmt->execute()) throw new Exception("Error during inserting kingdom on execution");

        // Return ID of inserted row
        return ($conn->lastInsertId());
    }

    // New person insertion
    function insertPerson ($name, $age, $title, $race_id, $desc_id, $culture_id) {
        global $conn;

        // Prepare statement
        $insert = "INSERT INTO people (name, age, title, race_id, desc_id, culture_id) VALUES (?,?,?,?,?,?)";
        $stmt = $conn->prepare($insert);

        // Bind values
        if(!$stmt->bindValue(1, $name, PDO::PARAM_STR)) throw new Exception("Error during inserting person on first bind.");
        if(!$stmt->bindValue(2, $age, PDO::PARAM_INT)) throw new Exception("Error during inserting person on second bind.");
        if(!$stmt->bindValue(3, $title, PDO::PARAM_STR)) throw new Exception("Error during inserting person on third bind.");
        if(!$stmt->bindValue(4, $race_id, PDO::PARAM_INT)) throw new Exception("Error during inserting person on fourth bind.");
        if(!$stmt->bindValue(5, $desc_id, PDO::PARAM_INT)) throw new Exception("Error during inserting person on fifth bind.");
        if(!$stmt->bindValue(6, $culture_id, PDO::PARAM_INT)) throw new Exception("Error during inserting person on sixth bind.");

        // Execute statement
        if(!$stmt->execute()) throw new Exception("Error during inserting person on execution");

        // Return ID of inserted row
        return ($conn->lastInsertId());
    }
    
    // New settlement insertion
    function insertSettlement ($name, $ruler_id, $type, $land_id, $desc_id, $location_id, $kingdom_id, $culture_id, $architecture_id) {
        global $conn;

        // Prepare statement
        $insert = "INSERT INTO settlements (name, ruler_id, type, land_id, desc_id, location_id, kingdom_id, culture_id, architecture_id) VALUES (?,?,?,?,?,?,?,?,?)";
        $stmt = $conn->prepare($insert);

        // Bind values
        if(!$stmt->bindValue(1, $name, PDO::PARAM_STR)) throw new Exception("Error during inserting person on first bind.");
        if(!$stmt->bindValue(2, $ruler_id, PDO::PARAM_INT)) throw new Exception("Error during inserting person on second bind.");
        if(!$stmt->bindValue(3, $type, PDO::PARAM_STR)) throw new Exception("Error during inserting person on third bind.");
        if(!$stmt->bindValue(4, $land_id, PDO::PARAM_INT)) throw new Exception("Error during inserting person on fourth bind.");
        if(!$stmt->bindValue(5, $desc_id, PDO::PARAM_INT)) throw new Exception("Error during inserting person on fifth bind.");
        if(!$stmt->bindValue(6, $location_id, PDO::PARAM_INT)) throw new Exception("Error during inserting person on sixth bind.");
        if(!$stmt->bindValue(7, $kingdom_id, PDO::PARAM_INT)) throw new Exception("Error during inserting person on seventh bind.");
        if(!$stmt->bindValue(8, $culture_id, PDO::PARAM_INT)) throw new Exception("Error during inserting person on eight bind.");
        if(!$stmt->bindValue(9, $architecture_id, PDO::PARAM_INT)) throw new Exception("Error during inserting person on ninth bind.");

        // Execute statement
        if(!$stmt->execute()) throw new Exception("Error during inserting person on execution");

        // Return ID of inserted row
        return ($conn->lastInsertId());
    }

    // Load and prepare data
    try {
        //Check if data exists, if not, throw exception
        if(isset($_GET["data"])) $data = prepare($_GET["data"]);
        else throw new Exception("There's no data to process.");
    }
    catch (Exception $e) {
        print_r($e->getMessage());
    }

    try {
        $conn->beginTransaction();

        // 0 => Map. 1 - name, 2 - path, 3 - height, 4 - width
        if ($data[0]==0 && count($data)==5) $retid = insertMap($data[1], $data[2], $data[3], $data[4]);

        // 1 => God. 1 - name, 2 - domain, 3 - shortdesc, 4 - longdesc
        else if ($data[0]==1 && count($data)==5) $retid = insertGod($data[1], $data[2], insertDesc($data[3], $data[4]));

        // 2 => Language. 1 - name, 2 - desc
        else if ($data[0]==2 && count($data)==3) $retid = insertLang($data[1], $data[2]);

        // 3 => Culture. 1 - name, 2 - shortdesc, 3 - longdesc
        else if ($data[0]==3 && count($data)==4) $retid = insertCulture($data[1], insertDesc($data[2], $data[3]));

        // 4 => Architecture style. 1 - name, 2 - shortdesc, 3 - longdesc
        else if ($data[0]==4 && count($data)==4) $retid = insertArchitecture($data[1], insertDesc($data[2], $data[3]));

        // 5 => Race. 1 - name, 2 - shortdesc, 3 - longdesc
        else if ($data[0]==5 && count($data)==4) $retid = insertRace($data[1], insertDesc($data[2], $data[3]));

        // 6 => Perk. 1 - name, 2 - desc, 3 - is_positive
        else if ($data[0]==6 && count($data)==4) $retid = insertPerk($data[1], $data[2], $data[3]);

        // 7 => Land. 1 - name, 2 - path, 3 - shortdesc, 4-longdesc
        else if ($data[0]==7 && count($data)==5) $retid = insertLand($data[1], $data[2], insertDesc($data[3], $data[4]));

        // 8 => Event. 1 - name, 2 - posX, 3 - posY, 4 - posZ, 5 - map_id, 6 - shortdesc, 7 - longdesc
        else if ($data[0]==8 && count($data)==8) $retid = insertEvent($data[1],insertMap($data[2],$data[3],$data[4],$data[5]),insertDesc($data[6],$data[7]));

        // 9 => Kingdom. 1 - name, 2 - ruler, 3 - type, 4 - shortdesc, 5 - longdesc, 6 - culture_id
        else if ($data[0]==9 && count($data)==7) $retid = insertKingdom($data[1], $data[2], $data[3], insertDesc($data[4], $data[5]), $data[6]);

        // 10 => Person. 1 - name, 2 - age, 3 - title, 4 - race_id, 5 - shortdesc, 6 - longdesc, 7 - culture_id
        else if ($data[0]==10 && count($data)==8) $retid = insertPerson($data[1], $data[2], $data[3], $data[4], insertDesc($data[5], $data[6]), $data[7]);

        // 11 => Settlement. 1 - name, 2 - ruler_id, 3 - type, 4 - land_id, 5 - shortdesc, 6 - longdesc, 7 - location_id, 8-kingdom_id, 9 - culture_id, 10 architecture_id
        else if ($data[0]==10 && count($data)==8) $retid = insertSettlement($data[1], $data[2], $data[3], $data[4], insertDesc($data[5], $data[6]), $data[7], $data[8], $data[9], $data[10]);

        // If nothing fits, given data is erronous. Throw error.
        else throw new Exception ("Błąd: Argumenty nie pasują do żadnego warunku.");

        $conn->commit();

        print_r("Operacja zakończona powodzeniem. ID:".$retid);
    }
    catch (Exception $e) {
        $conn->rollBack();
        print_r($e->getMessage());
    }
?>