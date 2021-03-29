<?php

    date_default_timezone_set('UTC');

    $user='sysadm';
    $pass='gtf0mate';
    $success=4;

    //Create or connect to database 
    try { 
        $conn = new PDO('sqlite:../db/central', $user, $pass);
        print_r("\nConnection estabilished.");
    }
    catch (Exception $e) {
        die("Unable to connect: ".$e->getMessage());
    }

    //Purging old tables
    try {
        $conn->beginTransaction();

        $conn->exec("DROP TABLE IF EXISTS settlements;");
        $conn->exec("DROP TABLE IF EXISTS people;");
        $conn->exec("DROP TABLE IF EXISTS perks;");
        $conn->exec("DROP TABLE IF EXISTS kingdoms;");
        $conn->exec("DROP TABLE IF EXISTS races;");
        $conn->exec("DROP TABLE IF EXISTS architecture;");
        $conn->exec("DROP TABLE IF EXISTS cultures;");
        $conn->exec("DROP TABLE IF EXISTS mapLocations;");
        $conn->exec("DROP TABLE IF EXISTS maps;");
        $conn->exec("DROP TABLE IF EXISTS perks;");
        $conn->exec("DROP TABLE IF EXISTS languages;");
        $conn->exec("DROP TABLE IF EXISTS descriptions;");

        $conn->commit();

        print_r("\nTables dropped successfully.");
        $success--;
    }
    catch (Exception $e) {
        $conn->rollBack();
        print_r("\nError: ".$e->getMessage());
    }

    try {
        $conn->beginTransaction();
        //List of description pairs used by pretty much every other table
        $conn->exec (
            "CREATE TABLE  descriptions (
                id integer primary key,
                shortdesc text,
                longdesc text
            );"
        );
        //List of languages
        $conn->exec (
            "CREATE TABLE languages (
                id integer primary key,
                name text not null unique,
                desc text not null
            );"
        );
        //List of both negative and positive perks
        $conn->exec (
            "CREATE TABLE perks (
                id integer primary key,
                name text not null unique,
                desc text,
                is_positive boolean not null default true
            );"
        );
        //List of uploaded maps.
        $conn->exec (
            "CREATE TABLE  maps (
                id integer primary key,
                name text not null unique,
                path text,
                height integer,
                width integer
            );"
        );
        $conn->commit();
        print_r("\nBasic tables created successfully.");
        $success--;
    }
    catch (Exception $e) {
        $conn->rollBack();
        print_r("\nError during basic table creation: ".$e->getMessage());
    }
    try {
        $conn->beginTransaction();
        //List of gods
        $conn->exec(
            "CREATE TABLE gods (
                id integer primary key,
                name text not null,
                domain text not null,
                desc_id integer not null,
                FOREIGN KEY (desc_id) REFERENCES descriptions (id) ON UPDATE CASCADE ON DELETE SET NULL
            );"
        );
        //List of map locations.
        //By default values under posX and posY should be divided by 100 before use (20393->203.93)
        //and new values should be multiplied by 100 before update (203.94->20394).
        //posZ is meant to work with z-index, allowing creation of layered map locations. ex. land under cities.
        $conn->exec (
            "CREATE TABLE  mapLocations (
                id integer primary key,
                posX integer,
                posY integer,
                posZ integer,
                map_id integer,
                FOREIGN KEY (map_id) REFERENCES maps (id) ON UPDATE CASCADE ON DELETE RESTRICT
            );"
        );
        //List of geographical lands
        $conn->exec (
            "CREATE TABLE  lands (
                id integer primary key,
                name text not null unique,
                path text,
                desc_id integer not null unique,
                FOREIGN KEY (desc_id) REFERENCES descriptions (id)  ON UPDATE CASCADE ON DELETE RESTRICT
            );"
        );
        //List of existing cultures
        $conn->exec (
            "CREATE TABLE  cultures (
                id integer primary key,
                name text not null unique,
                desc_id integer not null unique,
                FOREIGN KEY (desc_id) REFERENCES descriptions (id)  ON UPDATE CASCADE ON DELETE RESTRICT
            );"
        );
        //List of existing architectural styles
        $conn->exec (
            "CREATE TABLE  architecture (
                id integer primary key,
                name text not null unique,
                desc_id integer not null unique,
                FOREIGN KEY (desc_id) REFERENCES descriptions (id)  ON UPDATE CASCADE ON DELETE RESTRICT
            );"
        );
        //List of existing races
        $conn->exec (
            "CREATE TABLE  races (
                id integer primary key,
                name text not null unique,
                desc_id integer not null unique,
                FOREIGN KEY (desc_id) REFERENCES descriptions (id)  ON UPDATE CASCADE ON DELETE RESTRICT
            );"
        );
        //List of events
        $conn->exec(
            "CREATE TABLE events (
                id integer primary key,
                name text not null unique,
                location_id integer,
                desc_id integer not null unique,
                FOREIGN KEY (location_id) REFERENCES mapLocations (id) ON UPDATE CASCADE ON DELETE SET NULL,
                FOREIGN KEY (desc_id) REFERENCES descriptions (id) ON UPDATE CASCADE ON DELETE SET NULL
            );"
        );
        //List of existing kingdoms
        //type is the type of country, like monarchy, republic etc.
        $conn->exec (
            "CREATE TABLE  kingdoms (
                id integer primary key,
                name text not null unique,
                ruler text,
                type text,
                desc_id integer not null unique,
                culture_id integer,
                FOREIGN KEY (desc_id) REFERENCES descriptions (id)  ON UPDATE CASCADE ON DELETE RESTRICT,
                FOREIGN KEY (culture_id) REFERENCES cultures (id) ON UPDATE CASCADE ON DELETE SET NULL
            );"
        );
        //List of people of interest
        $conn->exec (
            "CREATE TABLE  people (
                id integer primary key,
                name text not null,
                age integer,
                title text,
                race_id integer not null,
                desc_id integer not null unique,
                culture_id integer not null,
                FOREIGN KEY (desc_id) REFERENCES descriptions (id)  ON UPDATE CASCADE ON DELETE RESTRICT,
                FOREIGN KEY (culture_id) REFERENCES cultures (id) ON UPDATE CASCADE ON DELETE RESTRICT
            );"
        );
        //List of existing settlements
        //type is meant as, for example, village, town, city, fortress.
        $conn->exec (
            "CREATE TABLE  settlements (
                id integer primary key,
                name text not null,
                ruler_id text,
                type text,
                land_id integer,
                desc_id integer not null unique,
                location_id integer,
                kingdom_id integer,
                culture_id integer,
                architecture_id integer,
                FOREIGN KEY (ruler_id) REFERENCES people (id) ON UPDATE CASCADE ON DELETE SET NULL,
                FOREIGN KEY (land_id) REFERENCES lands (id) ON UPDATE CASCADE ON DELETE SET NULL,
                FOREIGN KEY (desc_id) REFERENCES descriptions (id)  ON UPDATE CASCADE ON DELETE RESTRICT,
                FOREIGN KEY (location_id) REFERENCES mapLocations (id) ON UPDATE CASCADE ON DELETE SET NULL,
                FOREIGN KEY (kingdom_id) REFERENCES kingdoms (id) ON UPDATE CASCADE ON DELETE SET NULL,
                FOREIGN KEY (culture_id) REFERENCES cultures (id) ON UPDATE CASCADE ON DELETE SET NULL,
                FOREIGN KEY (architecture_id) REFERENCES architecture (id) ON UPDATE CASCADE ON DELETE SET NULL
            );"
        );
        $conn->commit();
        print_r("\nKeyed tables created successfully.");
        $success--;
    }
    catch (Exception $e) {
        $conn->rollBack();
        print_r("\nKeyed tables not created: ".$e->getMessage());
    }

    //Below are various needed N:N tables
    try {
        $conn->beginTransaction();
        $conn->exec (
            "CREATE TABLE  languages_cities (
                id integer primary key,
                language_id integer not null,
                FOREIGN KEY (language_id) REFERENCES languages (id) ON UPDATE CASCADE ON DELETE CASCADE,
                city_id integer not null ,
                FOREIGN KEY (city_id) REFERENCES cities (id) ON UPDATE CASCADE ON DELETE CASCADE
            );"
        );
        $conn->exec (
            "CREATE TABLE  languages_kingdoms (
                id integer primary key,
                language_id integer not null,
                FOREIGN KEY (language_id) REFERENCES languages (id) ON UPDATE CASCADE ON DELETE CASCADE,
                kingdom_id integer not null ,
                FOREIGN KEY (kingdom_id) REFERENCES kingdoms (id) ON UPDATE CASCADE ON DELETE CASCADE
            );"
        );
        $conn->exec (
            "CREATE TABLE  races_perks (
                id integer primary key,
                race_id integer not null,
                FOREIGN KEY (race_id) REFERENCES races (id) ON UPDATE CASCADE ON DELETE CASCADE,
                perk_id integer not null ,
                FOREIGN KEY (perk_id) REFERENCES perks (id) ON UPDATE CASCADE ON DELETE CASCADE
            );"
        );
        $conn->commit();
        print_r("\nN:N tables created successfully.");
        $success--;
    }
    catch (Exception $e) {
        $conn->rollBack();
        print_r("\nCreation of N:N tables failed: ".$e->getMessage());
    }

    if ($success==0) {
        print_r("\nReboot finished successfully.\n\nCreated tables: \n\n");    
        //prepare fetch
        $select = "SELECT name FROM sqlite_master WHERE type IN ('table') AND name NOT LIKE 'sqlite_%' ORDER BY 1";
        $stmt = $conn->prepare($select);
    
        //execute fetch
        $stmt->execute();
    
        //results
        $results = $stmt->fetchAll(PDO::FETCH_NUM);
        $counter=3;
        foreach($results as $row) {
            if($counter==0) {
                print_r("\n");
                $counter=3;
            }
            print_r($row[0]." ");
            $counter--;
        }
    }
    else {
        print_r("\nReboot unsuccessfull.");
    }
    $conn=null;
?>