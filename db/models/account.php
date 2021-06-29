<?php

    use Models\Model;

    class Account extends Model
    {

        public $code;
        public $matricule;
        public $name;
        public $firstname;
        public $created_at;

        public function __construct($code, $matricule, $name, $firstname, $created_at)
        {
            parent::__construct("account");

            $this->code = $code;
            $this->matricule = $matricule;
            $this->name = $name;
            $this->firstname = $firstname;
            $this->created_at = $created_at;
        }

        public function save()
        {
            $this->created_at = date("c");
            $this->insertModel(Transact::$tablename);
        }

        public function update()
        {
            $this->updateModel(Transact::$tablename, "matricule");
        }

        public function delete()
        {

        }
    }

?>