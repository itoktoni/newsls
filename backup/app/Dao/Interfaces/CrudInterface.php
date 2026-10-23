<?php

namespace App\Dao\Interfaces;

interface CrudInterface extends CreateInterface, DataInterface, DeleteInterface, SingleInterface, UpdateInterface
{
}
