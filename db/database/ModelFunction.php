<?php

    namespace Models;

    interface ModelFunction {

        /**
         * Function which save a model in database
         */
        public function save();

        /**
         * Function which update a model data in database
         */
        public function update();

        /**
         * Function which delete a model data in database
         */
        public function delete();

    }

?>