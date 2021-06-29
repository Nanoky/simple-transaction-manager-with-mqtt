<?php

    namespace Models;

    interface ModelInterface
    {
        public function all();
        public function findById(Array $id);
        public function save();
        public function update();
        public function delete();
    }

?>