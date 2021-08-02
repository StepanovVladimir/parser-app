<?php

namespace App\Repositories\Interfaces;

interface CollegeRepositoryInterface
{
    function getColleges();
    function updateColleges(array $newColleges);
}