<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Models\College;
use Goutte\Client;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    private const URL = 'https://www.princetonreview.com/college-search?ceid=cp-1022984';

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->call(function()
        {
            $newColleges = $this->parseColleges();
            $this->updateColleges($newColleges);
        })->hourly();
    }

    private function parseColleges(): array
    {
        $client = new Client();
        $listPage = $client->request('GET', self::URL);

        $colleges = array();
    
        $parseItem = function($item) use (&$client, &$colleges)
        {
            $title = $item->filter('.margin-top-none');
            $name = $title->text();

            $schoolImg = $item->filter('.school-image');
            $schoolImgLarge = $item->filter('.school-image-large');
            $image = $schoolImg->count() > 0
                ? $schoolImg->attr('src')
                : ($schoolImgLarge->count() > 0 ? $schoolImgLarge->attr('src') : null);

            [$city, $state] = explode(', ', $item->filter('.location')->text());

            $collegePage = $client->request('GET', $title->filter('a')->attr('href'));

            $schoolAddress = $collegePage->filter('.school-headline-address');
            $address = $schoolAddress->filter('span')->text();
            $site =$schoolAddress->filter('a')->attr('href');

            $phone = null;
            $schoolContacts = $collegePage->filter('.school-contacts .col-xs-6');
            for ($i = 0; $i < $schoolContacts->count(); $i++)
            {
                if ($schoolContacts->eq($i)->text() == 'Phone')
                {
                    $phone = $schoolContacts->eq($i + 1)->text();
                    break;
                }
            }

            $college = new College();
            $college->name = $name;
            $college->image = $image;
            $college->state = $state;
            $college->city = $city;
            $college->address = $address;
            $college->phone = $phone;
            $college->site = $site;

            array_push($colleges, $college);
        };

        $listPage->filter('.col-sm-height')->each($parseItem);
        $listPage->filter('.vertical-padding')->each($parseItem);

        return $colleges;
    }

    private function updateColleges(array $newColleges)
    {
        $oldColleges = College::all();
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
                $oldCollege->name = $newCollege->name;
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

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
