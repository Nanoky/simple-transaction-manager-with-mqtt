<?php

    use Models\Model;
    use Models\ModelInterface;

    class Account extends Model implements ModelInterface
    {

        public $code;
        public $matricule;
        public $name;
        public $firstname;
        public $created_at;

        public function __construct($code = "", $matricule = "", $name = "", $firstname = "", $created_at = "")
        {
            parent::__construct("account");

            $this->code = $code;
            $this->matricule = $matricule;
            $this->name = $name;
            $this->firstname = $firstname;
            $this->created_at = $created_at;
        }

        public function all()
        {
            return $this->getAll(Account::class);
        }

        public function findById(Array $id)
        {
            return $this->getById(Account::class, $id);
        }

        public function save()
        {
            $this->created_at = date("c");
            $this->insertModel(Account::$tablename);
        }

        public function update()
        {
            $this->updateModel(Account::$tablename, "matricule");
        }

        public function delete()
        {

        }
    }

?>