<?php

namespace App\Http\Controllers;

use App\Models\College;
use App\Repositories\Interfaces\CollegeRepositoryInterface;
use Illuminate\Http\Request;

class MainController extends Controller
{
    private $collegeRepository;

    public function __construct(CollegeRepositoryInterface $collegeRepository)
    {
        $this->collegeRepository = $collegeRepository;
    }

    public function index()
    {
        return view('welcome', ['colleges' => $this->collegeRepository->getColleges()]);
    }
}
