<?php

    date_default_timezone_set('UTC');

    // Connect
    $user='sysadm';
    $pass='gtf0mate';
    $location_id = 0;
    $desc_id = 0;
    try { 
        $conn = new PDO('sqlite:./db/central', $user, $pass);
    }
    catch (Exception $e) {
        die("Unable to connect: ".$e->getMessage());
    }

    function insertLocation($posX, $posY, $posZ) {
        global $conn;

        // Prepare statement
        $mapInsert = "INSERT INTO mapLocations (posX, posY, posZ) VALUES (?,?,?)";
        $stmt = $conn->prepare($mapInsert);

        // Bind parameters
        $err=$stmt->bindValue(1, $posX, PDO::PARAM_INT);
        if ($err) {
            $err=$stmt->bindValue(2, $posY, PDO::PARAM_INT);
            if ($err) {
                $err=$stmt->bindValue(3, $posZ, PDO::PARAM_INT);
                if (!$err) return "Error on third bind";
            }
            else return "Error on second bind";
        }
        else return "Error on first bind";

        // Inserting data
        $err=$stmt->execute();
        if (!$err) return "Error on execute";

        // Get ID of inserted row
        $id = $conn->lastInsertId();

        return (int) $id;
    }

    function insertDescription ($shortdesc, $longdesc) {
        global $conn;

        //Prepare statement
        $descInsert = "INSERT INTO descriptions (shortdesc, longdesc) VALUES (?,?)";
        $stmt = $conn->prepare($descInsert);

        //Bind parameters
        $err = $stmt->bindValue(1, $shortdesc, PDO::PARAM_STR);
        if ($err) {
            $err = $stmt->bindValue(2, $longdesc, PDO::PARAM_STR);
            if(!$err) return "Error on second bind";
        }
        else return "Error on first bind";

        //Insert data
        $err = $stmt->execute();
        if(!$err) return "Error on execute";
        
        // Get ID of inserted row
        $id = $conn->lastInsertId();

        return (int) $id;
    }

    //...here goes the main pain in the ass
    function insertSettlement ($name, $ruler_id, $type, $url, $land_id, $desc_id, $kingdom_id, $culture_id, $architecture_id, $location_id) {
        global $conn;

        //Prepare statement
        $descInsert = "INSERT INTO settlements (name, ruler_id, type, land_id, desc_id, location_id, kingdom_id, culture_id, architecture_id)
        VALUES (:name, :ruler_id, :type, :land_id, :desc_id, :location_id, :kingdom_id, :culture_id, :architecture_id)";
        $stmt = $conn->prepare($descInsert);

        //Bind parameters
        $err = $stmt->bindValue(":name", $name, PDO::PARAM_STR);
        if (!$err) return "Error on binding of name";
        $err = $stmt->bindValue(":ruler_id", $ruler_id, PDO::PARAM_INT);
        if (!$err) return "Error on binding of ruler";
        $err = $stmt->bindValue(":type", $type, PDO::PARAM_STR);
        if (!$err) return "Error on binding of type";
        $err = $stmt->bindValue(":land_id", $land_id, PDO::PARAM_INT);
        if (!$err) return "Error on binding of land";
        $err = $stmt->bindValue(":desc_id", $desc_id, PDO::PARAM_INT);
        if (!$err) return "Error on binding of desc";
        $err = $stmt->bindValue(":location_id", $location_id, PDO::PARAM_INT);
        if (!$err) return "Error on binding of loc";
        $err = $stmt->bindValue(":kingdom_id", $kingdom_id, PDO::PARAM_INT);
        if (!$err) return "Error on binding of kingdom";
        $err = $stmt->bindValue(":culture_id", $culture_id, PDO::PARAM_INT);
        if (!$err) return "Error on binding of culture";
        $err = $stmt->bindValue(":architecture_id", $architecture_id, PDO::PARAM_INT);
        if (!$err) return "Error on binding of architecture";

        //Insert data
        $err = $stmt->execute();
        if(!$err) return "Error on execute";
        
        // Get ID of inserted row
        $id = $conn->lastInsertId();

        return (int) $id;
    }

    // Load data
    //$data = $_POST["data"];
    $data = $_POST["data"];

    // Prepare data
    list($name, $ruler_id, $type, $url, $land_id, $shortdesc, $longdesc, $kingdom_id, $culture_id, $architecture_id, $posX, $posY, $posZ) = explode("!", $data);

    try {
        $conn->beginTransaction();

        //Insert location
        $location_id=insertLocation($posX, $posY, $posZ);
        if(is_string($location_id)) throw new Exception("Error during location insertion: ".$location_id);

        //Insert description
        $desc_id=insertDescription($shortdesc, $longdesc);
        if(is_string($desc_id)) throw new Exception("Error during description insertion: ".$desc_id);
        
        //Insert settlement
        $settl_id=insertSettlement($name, $ruler_id, $type, $url, $land_id, $desc_id, $kingdom_id, $culture_id, $architecture_id, $location_id);
        if(is_string($settl_id)) throw new Exception("Error during settlement insertion: ".$settl_id);
        
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