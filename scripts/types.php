<?php
    // Below you'll find classes created for each of the table on site. These are for pre defined db tables only.

    abstract class DBTable {
        protected $DBdata;
        protected $tablename;
        protected $insert;
        protected $update;

        abstract function __construct(); 

        abstract function __set($name, $value); // Setter of values. Allow only DB values, except the ID. ID should only be settable by fetching or inserting

        public function id() {
            return $this->DBdata["id"];
        } // For simple ID return

        abstract function present(); // Meant to return tablename and db data of object as array


        private function inject ($conn, $type) {
            // Check if there's data
            $is_data=true;
            foreach ($this->DBdata as $key=>$val){
                if ($key!="id" && $val==null) $is_data=false;
            } 
            if($is_data) {

                // Prepare statement
                if ($type==0) if (!($stmt = $conn->prepare($this->insert))) throw new Exception ("Error during statement preparation in ".$this->tablename." type 0");
                if ($type==1) if (!($stmt = $conn->prepare($this->update))) throw new Exception ("Error during statement preparation in ".$this->tablename." type 1");

                // Bind values
                foreach ($this->DBdata as $key => $val) {
                    if ($key=="id"&&$type==0) continue; // Skip ID binding if we are preparing for insertion (no id yet for row)
                    if (is_numeric($val)) if (!$stmt->bindValue(":".$key, $val, PDO::PARAM_INT)) throw new Exception ("Error during ".$this->tablename." injection on ".$key." bind");
                    if (is_bool($val)) if (!$stmt->bindValue(":".$key, $val, PDO::PARAM_BOOL)) throw new Exception ("Error during ".$this->tablename." injection on ".$key." bind");
                    if (is_string($val)) if (!$stmt->bindValue(":".$key, $val, PDO::PARAM_STR)) throw new Exception ("Error during ".$this->tablename." injection on ".$key." bind");
                }
                
                // Inject data, if it's a success return id, else throw exception
                if (!$stmt->execute()) {
                    throw new Exception ("Error during description injection on execution");
                } 
                else {
                    if($type==0) $this->DBdata["id"] = $conn->lastInsertId();
                }
            }
            else throw new Exception ("Error. Injection failed. No data given.");
        } // Inserting or Updating of row in DB. Accessed by either insert method or update method
        
        public function insert($conn) {
            $this->inject($conn, 0);
        } // Insertion of row into table

        public function fetch($conn, $columns, $where_args, $limit, $offset) {
            // Check if columns variable isn't empty or zero, if it is get all columns
            if ($columns==0||$columns==null) $columns="*";
            // Prepare select instruction
            $select = "SELECT ".$columns." FROM ".$this->tablename;
            // If where_args isn't empty, append it to rest of instruction.
            if (!($where_args==0 || $where_args==null)) {
                $select .= " WHERE ".$where_args;
            }
            $select .= " ORDER BY id";
            // If limit is given, use it. If offset is given with limit, use it too. Else skip.
            if ($limit!=0 || $limit!=null) $select .= ($offset!=0 || $offset!=null)? " LIMIT ".$offset.", ".$limit : " LIMIT ".$limit;
            $select .= ";";
            // Prepare for execution, if fails throw exception
            if(!($stmt = $conn->prepare($select))) throw new Exception("Error on preparation of statement. ".$select);
            
            // Execute statement
            $stmt->execute();

            // Return data
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $results;
        } // Retrieve data from table. Columns are literally what columns to retrieve, where_args are meant to be string (ex. "id = 1")
        //limit and offset are optional and are mainly there for pagination.

        public function fetchSimple ($conn, $where_args) {
            return $this->fetch($conn, 0, $where_args, 0, 0);
        } // Just a method to make using fetch require less arguments for clarity's sake

        public function update($conn, $id){
            if (is_numeric($id)) {
                $this->DBdata["id"] = $id;
                $this->inject($conn, 1);
            }
            else throw new Exception ("ID needs to be of numeric value");
        } // Update row with given ID

        public function delete($conn, $id) {
            if (is_numeric($id)) {
                $conn->exec( "DELETE FROM ".$this->tablename." WHERE id=".$id.";" );
            }
            else throw new Exception ("ID can only be a numeric value.");
        } // Removal of row from table

        abstract function createTable($conn); // Function for table creation

        public function deleteTable($conn) {
            $conn->exec("DROP TABLE IF EXISTS ".$this->tablename.";");
        } // Function for dropping tables
    }

    class Description extends DBTable {
        // Description table used for pretty much every other table
        protected $DBdata = ["id"=>"", "shortdesc"=>"", "longdesc"=>""]; // Data for insertion/updating in table
        protected $tablename = "descriptions";
        protected $insert = "INSERT INTO descriptions (shortdesc, longdesc) VALUES (:shortdesc, :longdesc)";
        protected $update = "UPDATE descriptions SET shortdesc = :shortdesc, longdesc = :longdesc WHERE id = :id";

        public function __construct() {
            $argv = func_get_args();
            if (func_num_args()==2 ){
                $this->DBdata["shortdesc"] = $argv[0];
                $this->DBdata["longdesc"] = $argv[1];
            }
            elseif (func_num_args() == 0) { }
            else throw new Exception("Error: Wrong number of arguments.");
        }

        public function __set($name, $value) {
            switch($name) {
                case 'shortdesc': $this->DBdata["shortdesc"] = $value; break;
                case 'longdesc': $this->DBdata["longdesc"] = $value; break;
            }
        }

        public function present() {
            return [ "tablename" => $this->tablename, "id" => $this->DBdata["id"], "shortdesc" => $this->DBdata["shortdesc"], "longdesc" => $this->DBdata["longdesc"] ];
        }

        public function createTable($conn) {
            $conn->exec(
                "CREATE TABLE  ".$this->tablename." (
                    id integer primary key,
                    shortdesc text,
                    longdesc text
                );"
            );
        }
    }

    class Language extends DBTable {
        // Languages descripted on the site
        protected $DBdata = ["id" => "", "name" => "", "desc_id" => ""];
        protected $tablename = "languages";
        protected $insert = "INSERT INTO languages (name, desc_id)  VALUES (:name,:desc_id)";
        protected $update = "UPDATE languages SET name = :name, desc_id = :desc_id WHERE id = :id";

        public function __construct() {
            $argv = func_get_args();
            if (func_num_args()==2 ){
                $this->DBdata["name"] = $argv[0];
                $this->DBdata["desc_id"] = $argv[1];
            }
            elseif (func_num_args() == 0) { }
            else throw new Exception("Error: Wrong number of arguments.");
        }

        public function __set($name, $value) {
            switch($name) {
                case 'name': $this->DBdata["name"] = $value; break;
                case 'desc_id': $this->DBdata["desc_id"] = $value; break;
            }
        }

        public function present() {
            return [ "tablename" => $this->tablename, "id" => $this->DBdata["id"], "name" => $this->DBdata["name"], "desc_id" => $this->DBdata["desc_id"] ];
        }

        public function createTable($conn) {
            $conn->exec(
                "CREATE TABLE  ".$this->tablename." (
                    id integer primary key,
                    name text,
                    desc_id int,
                    FOREIGN KEY (desc_id) REFERENCES descriptions (id) ON UPDATE CASCADE ON DELETE CASCADE
                );"
            );
        }
    }

    class Perk extends DBTable {
        // Perks added to races, later maybe would be used for character creator
        protected $DBdata = ["id" => "", "name"=>"", "desc_id"=>"", "is_positive"=>""];
        protected $tablename = "perks";
        protected $insert = "INSERT INTO perks (name, desc_id, is_positive)  VALUES (:name,:desc_id,:is_positive)";
        protected $update = "UPDATE perks SET name = :name, desc_id = :desc_id, is_positive = :is_positive WHERE id = :id";

        public function __construct() {
            $argv = func_get_args();
            if (func_num_args()==3 ){
                $this->DBdata["name"] = $argv[0];
                $this->DBdata["desc_id"] = $argv[1];
                $this->DBdata["is_positive"] = $argv[2];
            }
            elseif (func_num_args() == 0) { }
            else throw new Exception("Error: Wrong number of arguments.");
        }

        public function __set($name, $value) {
            switch($name) {
                case 'name': $this->DBdata["name"] = $value; break;
                case 'desc_id': $this->DBdata["desc_id"] = $value; break;
                case 'is_positive': $this->DBdata["is_positive"] = $value; break;
            }
        }

        public function present() {
            return [ "tablename" => $this->tablename, "id" => $this->DBdata["id"], "name" => $this->DBdata["name"], "desc_id" => $this->DBdata["desc_id"], "is_positive" => $this->DBdata["is_positive"] ];
        }

        public function createTable($conn) {
            $conn->exec(
                "CREATE TABLE  ".$this->tablename." (
                    id integer primary key,
                    name text not null unique,
                    desc_id int,
                    is_positive boolean not null default true,
                    FOREIGN KEY (desc_id) REFERENCES descriptions (id) ON UPDATE CASCADE ON DELETE CASCADE
                );"
            );
        }
    }

    class Map extends DBTable {
        // Perks added to races, later maybe would be used for character creator
        protected $DBdata = ["id"=>"", "name"=>"", "path"=>"", "height"=>"", "width"=>""];
        protected $tablename = "maps";
        protected $insert = "INSERT INTO maps (name, path, height, width) VALUES (:name,:path,:height,:width)";
        protected $update = "UPDATE maps SET name = :name, path = :path, height = :height, width = :width WHERE id = :id";

        public function __construct() {
            $argv = func_get_args();
            if (func_num_args()==4 ){
                $this->DBdata["name"] = $argv[0];
                $this->DBdata["path"] = $argv[1];
                $this->DBdata["height"] = $argv[2];
                $this->DBdata["width"] = $argv[3];
            }
            elseif (func_num_args() == 0) { }
            else throw new Exception("Error: Wrong number of arguments.");
        }

        public function __set($name, $value) {
            switch($name) {
                case 'name': $this->DBdata["name"] = $value; break;
                case 'path': $this->DBdata["path"] = $value; break;
                case 'height': $this->DBdata["height"] = $value; break;
                case 'width': $this->DBdata["width"] = $value; break;
            }
        }

        public function present() {
            return [ "tablename" => $this->tablename, "id" => $this->DBdata["id"], "name" => $this->DBdata["name"], "path" => $this->DBdata["path"], "height" => $this->DBdata["height"], "width" => $this->DBdata["width"] ];
        }

        public function createTable($conn) {
            $conn->exec(
                "CREATE TABLE  ".$this->tablename." (
                    id integer primary key,
                    name text not null unique,
                    path text,
                    height integer,
                    width integer
                );"
            );
        }
    }

    class God extends DBTable {
        // Perks added to races, later maybe would be used for character creator
        protected $DBdata = ["id"=>"", "name"=>"", "domain"=>"", "desc_id"=>""];
        protected $tablename = "gods";
        protected $insert = "INSERT INTO gods (name, domain, desc_id) VALUES (:name,:domain,:desc_id)";
        protected $update = "UPDATE gods SET name = :name, domain = :domain, desc_id = :desc_id WHERE id = :id";

        public function __construct() {
            $argv = func_get_args();
            if (func_num_args()==3 ){
                $this->DBdata["name"] = $argv[0];
                $this->DBdata["domain"] = $argv[1];
                $this->DBdata["desc_id"] = $argv[2];
            }
            elseif (func_num_args() == 0) { }
            else throw new Exception("Error: Wrong number of arguments.");
        }

        public function __set($name, $value) {
            switch($name) {
                case 'name': $this->DBdata["name"] = $value; break;
                case 'domain': $this->DBdata["domain"] = $value; break;
                case 'desc_id': $this->DBdata["desc_id"] = $value; break;
            }
        }

        public function present() {
            return [ "tablename" => $this->tablename, "id" => $this->DBdata["id"], "name" => $this->DBdata["name"], "domain" => $this->DBdata["domain"], "desc_id" => $this->DBdata["desc_id"] ];
        }

        public function createTable($conn) {
            $conn->exec(
                "CREATE TABLE  ".$this->tablename." (
                    id integer primary key,
                    name text not null,
                    domain text not null,
                    desc_id integer not null,
                    FOREIGN KEY (desc_id) REFERENCES descriptions (id) ON UPDATE CASCADE ON DELETE CASCADE
                );"
            );
        }
    }

    class Land extends DBTable {
        // Perks added to races, later maybe would be used for character creator
        protected $DBdata = ["id"=>"", "name"=>"", "path"=>"", "map_id"=>"", "desc_id"=>""];
        protected $tablename = "lands";
        protected $insert = "INSERT INTO lands (name, path, map_id, desc_id) VALUES (:name,:path,:map_id,:desc_id)";
        protected $update = "UPDATE gods SET name = :name, path = :path, map_id = :map_id, desc_id = :desc_id WHERE id = :id";

        public function __construct() {
            $argv = func_get_args();
            if (func_num_args()==4 ){
                $this->DBdata["name"] = $argv[0];
                $this->DBdata["path"] = $argv[1];
                $this->DBdata["map_id"] = $argv[2];
                $this->DBdata["desc_id"] = $argv[3];
            }
            elseif (func_num_args() == 0) { }
            else throw new Exception("Error: Wrong number of arguments.");
        }

        public function __set($name, $value) {
            switch($name) {
                case 'name': $this->DBdata["name"] = $value; break;
                case 'path': $this->DBdata["path"] = $value; break;
                case 'map_id': $this->DBdata["map_id"] = $value; break;
                case 'desc_id': $this->DBdata["desc_id"] = $value; break;
            }
        }

        public function present() {
            return [ "tablename" => $this->tablename, "id" => $this->DBdata["id"], "name" => $this->DBdata["name"], "path" => $this->DBdata["path"], "map_id" => $this->DBdata["map_id"], "desc_id" => $this->DBdata["desc_id"] ];
        }

        public function createTable($conn) {
            $conn->exec(
                "CREATE TABLE  ".$this->tablename." (
                    id integer primary key,
                    name text not null unique,
                    path text,
                    map_id text,
                    desc_id integer not null unique,
                    FOREIGN KEY (map_id) REFERENCES maps (id) ON UPDATE CASCADE ON DELETE CASCADE,
                    FOREIGN KEY (desc_id) REFERENCES descriptions (id)  ON UPDATE CASCADE ON DELETE RESTRICT
                );"
            );
        }
    }
?>