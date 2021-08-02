<?php

namespace App\Repositories;

use App\Models\College;
use App\Repositories\Interfaces\CollegeRepositoryInterface;

class CollegeRepository implements CollegeRepositoryInterface
{
    public function getColleges()
    {
        return College::all();
    }

    public function updateColleges(array $newColleges)
    {
        $oldColleges = $this->getColleges();
        foreach ($oldColleges as $college)
        {
            if (!$this->anyCollege($newColleges, $college))
            {
                $college->delete();
            }
        }

        foreach ($newColleges as $newCollege)
        {
            $oldCollege = College::firstWhere('name', $newCollege->name);
            if (!$oldCollege)
            {
                $newCollege->save();
            }
            else
            {
                $oldCollege->image = $newCollege->image;
                $oldCollege->state = $newCollege->state;
                $oldCollege->city = $newCollege->city;
                $oldCollege->address = $newCollege->address;
                $oldCollege->phone = $newCollege->phone;
                $oldCollege->site = $newCollege->site;

                $oldCollege->save();
            }
        }
    }

    private function anyCollege(array $colleges, College $college): bool
    {
        foreach ($colleges as $c)
        {
            if ($c->name == $college->name)
            {
                return true;
            }
        }

        return false;
    }
}