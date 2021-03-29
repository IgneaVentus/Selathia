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
        $predone = preg_replace("/[\[\]\*\{\}\(\)\+\|\^\&\.\\/?<>'\"]/s", "", $data);
        // Remove whitespaces
        $done = preg_replace("/\s{2,}/s", " ", $predone);
        
        // Explode and return data for further processing
        return explode("!", trim($done));
    }

    function insertLocation($posX, $posY, $posZ) {
        global $conn;

        // Prepare statement
        $mapInsert = "INSERT INTO mapLocations (posX, posY, posZ) VALUES (?,?,?)";
        $stmt = $conn->prepare($mapInsert);

        // Bind parameters
        if (!$stmt->bindValue(1, $posX, PDO::PARAM_INT)) throw new Exception("Error during insertion of location on first bind");
        if (!$stmt->bindValue(2, $posY, PDO::PARAM_INT)) throw new Exception("Error during insertion of location on second bind");
        if (!$stmt->bindValue(3, $posZ, PDO::PARAM_INT)) throw new Exception("Error during insertion of location on third bind");

        // Inserting data
        if (!$stmt->execute()) throw new Exception("Error during insertion of location on execute");

        // Return ID of inserted row
        return ($conn->lastInsertId());
    }

    function insertDescription ($shortdesc, $longdesc) {
        global $conn;

        //Prepare statement
        $descInsert = "INSERT INTO descriptions (shortdesc, longdesc) VALUES (?,?)";
        $stmt = $conn->prepare($descInsert);

        //Bind parameters
        if (!$stmt->bindValue(1, $shortdesc, PDO::PARAM_STR)) throw new Exception("Error during insertion of description on first bind");
        if (!$stmt->bindValue(2, $longdesc, PDO::PARAM_STR)) throw new Exception("Error during insertion of description on second bind");

        //Insert data
        if(!$stmt->execute()) throw new Exception("Error during insertion of description on execute");
        
        // Return ID of inserted row
        return ($conn->lastInsertId());
    }

    function insertSettlement ($name, $ruler_id, $type, $url, $land_id, $desc_id, $kingdom_id, $culture_id, $architecture_id, $location_id) {
        global $conn;

        //Prepare statement
        $descInsert = "INSERT INTO settlements (name, ruler_id, type, land_id, desc_id, location_id, kingdom_id, culture_id, architecture_id)
        VALUES (:name, :ruler_id, :type, :land_id, :desc_id, :location_id, :kingdom_id, :culture_id, :architecture_id)";
        $stmt = $conn->prepare($descInsert);

        //Bind parameters
        if (!$stmt->bindValue(":name", $name, PDO::PARAM_STR)) throw new Exception("Error during settlement insertion on binding of name");
        if (!$stmt->bindValue(":ruler_id", $ruler_id, PDO::PARAM_INT)) throw new Exception("Error during settlement insertion on binding of ruler");
        if (!$stmt->bindValue(":type", $type, PDO::PARAM_STR)) throw new Exception("Error during settlement insertion on binding of type");
        if (!$stmt->bindValue(":land_id", $land_id, PDO::PARAM_INT)) throw new Exception("Error during settlement insertion on binding of land");
        if (!$stmt->bindValue(":desc_id", $desc_id, PDO::PARAM_INT)) throw new Exception("Error during settlement insertion on binding of desc");
        if (!$stmt->bindValue(":location_id", $location_id, PDO::PARAM_INT)) throw new Exception("Error during settlement insertion on binding of loc");
        if (!$stmt->bindValue(":kingdom_id", $kingdom_id, PDO::PARAM_INT)) throw new Exception("Error during settlement insertion on binding of kingdom");
        if (!$stmt->bindValue(":culture_id", $culture_id, PDO::PARAM_INT)) throw new Exception("Error during settlement insertion on binding of culture");
        if (!$stmt->bindValue(":architecture_id", $architecture_id, PDO::PARAM_INT)) throw new Exception("Error during settlement insertion on binding of architecture");

        //Insert data
        if(!$stmt->execute()) throw new Exception("Error during settlement insertion on execute");
        
        // Return ID of inserted row
        return ($conn->lastInsertId());
    }

    // Load and prepare data
    try {
        //Check if data exists, if not, throw exception
        if(isset($_GET["data"])) list($name, $ruler_id, $type, $url, $land_id, $shortdesc, $longdesc, $kingdom_id, $culture_id, $architecture_id, $posX, $posY, $posZ) = prepare($_GET["data"]);
        else throw new Exception("There's no data to process.");

    }
    catch (Exception $e) {
        print_r($e->getMessage());
    }

    try {
        $conn->beginTransaction();

        //Insert settlement
        $settl_id=insertSettlement($name, $ruler_id, $type, $url, $land_id, insertDescription($shortdesc, $longdesc), $kingdom_id, $culture_id, $architecture_id, insertLocation($posX, $posY, $posZ));
        
        $conn->commit();
    }
    catch (Exception $e) {
        $conn->rollBack();
        die($e->getMessage());
    }

    $select = "SELECT s.name, s.type, d.shortdesc, d.longdesc, m.posX, m.posY, m.posZ FROM settlements s LEFT OUTER JOIN descriptions d ON s.desc_id = d.id LEFT OUTER JOIN mapLocations m ON s.location_id = m.id WHERE s.id =".$settl_id;
    $stmt = $conn->prepare($select);
    $stmt->execute();
    $results = $stmt->fetch(PDO::FETCH_ASSOC);
    print_r($results);
?>