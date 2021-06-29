<?php

    namespace Models;

    abstract class Model implements ModelFunction
    {

        protected $db;
        protected $tablename;

        public function __construct(String $tablename)
        {
            global $config;
            
            $this->db = new DB($config);
            $this->tablename = $tablename;
        }

        /**
         * Function which insert a model in a table of database
         */
        protected function insertModel()
        {
            try {
                $data = (Array) $this;
                unset($data[array_key_last($data)]);

                $query = "INSERT INTO " . $this->tablename . "(";
                $values = "(";

                foreach ($data as $key => $value) {
                    if (!empty($value) && !str_contains($key, "*") && (empty($value) || $value != -1))
                    {
                        $query = $query . $key . ",";
                        $values = $values . ":" . $key . ",";
                        continue;
                    }
                    
                    unset($data[$key]);
                }

                $query = substr($query, 0, -1);
                $values = substr($values, 0, -1);

                $query = $query . ") VALUES" . $values . ")";

                error_log($query);
                error_log(json_encode($data));

                return $this->db->set($query, $data);
            } catch (\Throwable $th) {
                throw $th;
            }
        }

        /**
         * Function which update a model in a table of database
         * @param mixed $id_name Table primary key name
         * @param $as_many_id Is true if the table as many field as primary key else false
         */
        protected function updateModel($id_name, $as_many_id = false)
        {
            try {
                $data = (Array) $this;
                unset($data[array_key_last($data)]);

                $query = "UPDATE " . $this->tablename . " SET ";

                foreach ($data as $key => $value) {
                    if (!str_contains($key, "*"))
                    {
                        if ($key != $id_name)
                            $query = $query . $key . "=:" . $key . ",";

                        continue;
                    }
                    unset($data[$key]);
                }

                $query = substr($query, 0, -1);
                $query = $query . " WHERE ";

                if ($as_many_id)
                {
                    foreach ($id_name as $key => $value) {
                        $query = $query . $id_name . "=:" . $id_name;
                        $query = $query . " AND ";
                    }

                    $query = substr($query, 0, -5);
                }
                else
                {
                    $query = $query . $id_name . "=:" . $id_name;
                }

                return $this->db->set($query, $data);
            } catch (\Throwable $th) {
                throw $th;
            }
        }

        /**
         * Function which delete a model in a table of database
         * @param mixed $id_name Table primary key name
         * @param $as_many_id Is true if the table as many field as primary key else false
         */
        protected function deleteModel($id_name, $as_many_id = false)
        {
            try {
                $data = (Array) $this;
                $query = "DELETE FROM " . $this->tablename . " WHERE ";

                if ($as_many_id)
                {
                    foreach ($id_name as $key => $value) {
                        $query = $query . $id_name . "=:" . $id_name;
                        $query = $query . " AND ";
                    }

                    $query = substr($query, 0, -5);
                }
                else
                {
                    $query = $query . $id_name . "=:" . $id_name;
                }
                
                $data = [
                    $id_name => $data[$id_name],
                ];

                return $this->db->set($query, $data);
            } catch (\Throwable $th) {
                throw $th;
            }
        }

        /**
         * Function which get all line in a table of database
         * @param $class Model class
         */
        public function getAll($class)
        {
            try {
                $result = $this->db->select("SELECT * FROM " . $this->tablename);

                $output = [];
                foreach ($result as $key => $value) {
                    $output[] = new $class($value);
                }

                return $output;
            } catch (\Throwable $th) {
                throw $th;
            }
        }

        /**
         * Function which get a line in a table of database from his id
         * @param $class Model class
         */        
        public function getById($class, Array $id)
        {
            try {
                $query = "SELECT * FROM " . $this->tablename ." WHERE ";

                foreach ($id as $key => $value) {
                    $query = $query . $key . "=:" . $key . " AND ";
                }
                $query = substr($query, 0, -5);

                $result = $this->db->select($query, $id);

                $output = [];
                foreach ($result as $key => $value) {
                    $output[] = new $class($value);
                }

                if (count($output) == 1)
                {
                    $output = $output[0];
                }
                return $output;
            } catch (\Throwable $th) {
                throw $th;
            }
        }

        public function getLastId()
        {
            return $this->db->getLastId();
        }

    }
    

?>