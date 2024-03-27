<?php

namespace classes;
require_once 'Insect.php';

class Beetle extends Insect
{
    function __construct()
    {
        parent::__construct("B");
    }
}