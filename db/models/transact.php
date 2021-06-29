<?php

    use Models\Model;
    use Models\ModelInterface;

    class Transact extends Model implements ModelInterface
    {

        public $code;
        public $date;

        public function __construct($code, $date)
        {
            parent::__construct("transact");

            $this->code = $code;
            $this->date = $date;
        }

        public function all()
        {
            return $this->getAll(Transact::class);
        }

        public function findById(Array $id)
        {
            return $this->getById(Transact::class, $id);
        }

        public function save()
        {
            $this->insertModel();
        }

        public function update()
        {

        }

        public function delete()
        {

        }
    }

?>