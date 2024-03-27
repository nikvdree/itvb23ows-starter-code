<?php

namespace classes;
require_once 'Insect.php';

class Queen extends Insect
{
    function __construct()
    {
        parent::__construct("Q");
    }
}