<?php

namespace App\Utils;

use App\Models\College;
use Goutte\Client;

class CollegesParser
{
    private const URL = 'https://www.princetonreview.com/college-search?ceid=cp-1022984';

    public function parseColleges(): array
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
}