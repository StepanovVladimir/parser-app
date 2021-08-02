<?php

namespace App\Console\Commands;

use App\Repositories\Interfaces\CollegeRepositoryInterface;
use App\Utils\CollegesParser;
use Illuminate\Console\Command;

class UpdateCollegesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'colleges:update';

    private $collegesParser;
    private $collegeRepository;

    public function __construct(CollegesParser $collegesParser, CollegeRepositoryInterface $collegeRepository)
    {
        parent::__construct();

        $this->collegesParser = $collegesParser;
        $this->collegeRepository = $collegeRepository;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $newColleges = $this->collegesParser->parseColleges();
        $this->collegeRepository->updateColleges($newColleges);
    }
}