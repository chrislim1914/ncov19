<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Goutte\Client;

class ScrapController extends Controller
{
    public function getNVOCAll() {       
        $dataholder = [];
        $wmdata = $this->getNCOVData();
        if ($wmdata) {
            $counter = 0;
            do {
                $dataholder[$wmdata[$counter + 0]] = array(
                    'totalcases'    => $wmdata[$counter + 1],
                    'newcases'    => $wmdata[$counter + 2],
                    'totaldeath'    => $wmdata[$counter + 3],
                    'newdeath'    => $wmdata[$counter + 4],
                    'totalrecovery'    => $wmdata[$counter + 5],
                    'activecases'    => $wmdata[$counter + 6],
                    'serious_critical'    => $wmdata[$counter + 7],
                    'casesPerOneMillion'    => $wmdata[$counter + 8],
                    'deathPerOneMillion'    => $wmdata[$counter + 9],
                );    
                $counter += 10;
            } while(count($wmdata) > $counter);

            return response()->json([
                'data'      => $dataholder,
                'result'    => true
            ]);
        }

        return response()->json([
            'message'   => 'Error getting data',
            'result'    => false
        ]);        
    }    

    public function getNCOVByCountry(Request $request) {
        $byCountry = [];
        $wmdata = $this->getNCOVData();
        $country = ucfirst($request->country);
        if (in_array($country, $this->getNCOVCountries())) {
            $counter = 0;
            do {
                if ($wmdata[$counter + 0] === $country) {                    
                    $byCountry[$wmdata[$counter + 0]] = array(
                        'totalcases'    => $wmdata[$counter + 1],
                        'newcases'    => $wmdata[$counter + 2],
                        'totaldeath'    => $wmdata[$counter + 3],
                        'newdeath'    => $wmdata[$counter + 4],
                        'totalrecovery'    => $wmdata[$counter + 5],
                        'activecases'    => $wmdata[$counter + 6],
                        'serious_critical'    => $wmdata[$counter + 7],
                        'casesPerOneMillion'    => $wmdata[$counter + 8],
                        'deathPerOneMillion'    => $wmdata[$counter + 9],
                    );  
                }  
                $counter += 10;
            } while(count($wmdata) > $counter);

            return response()->json([
                'data'      => $byCountry,
                'result'    => true
            ]);
        }

        return response()->json([
            'message'   => 'Country not found',
            'result'    => false
        ]);
    }

    public function getNCOVAllCountry() {
        $countries = $this->getNCOVCountries();
        if ($countries) {
            return response()->json([
                'data'      => $countries,
                'result'    => true
            ]);
        }

        return response()->json([
            'message'   => 'Error getting data',
            'result'    => false
        ]);
    }

    protected function getNCOVCountries() {
        $countryholder = [];
        $wmdata = $this->getNCOVData();
        if ($wmdata) {
            $counter = 0;
            do {
                if ($wmdata[$counter + 0] !== 'Total:') {
                    array_push($countryholder, $wmdata[$counter + 0]);
                }                
                $counter += 10;
            } while(count($wmdata) > $counter);

            return $countryholder;
        }

        return false;  
    }

    protected function getNCOVData() {
        $scrapping_url = 'https://www.worldometers.info/coronavirus/';
        $client = new Client();
        $crawler = $client->request('GET', $scrapping_url);

        $wm = $crawler->filter('.main_table_countries tr td')->each(function ($node) {
            return $node->text();
        });

        if ($wm) {
            return $wm;
        }
        return false;
    }
}
