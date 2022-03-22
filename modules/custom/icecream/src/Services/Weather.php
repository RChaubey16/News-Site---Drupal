<?php

namespace  Drupal\icecream\Services;



class Weather
{
    public function getWeather($city){
        $apiKey = \Drupal::config('icecream.settings')->get('weather_apiKey');
        $city = $city;
        $googleApiUrl = "http://api.openweathermap.org/data/2.5/weather?q=".$city.",IN,IN&units=metric&appid=".$apiKey;

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_URL, $googleApiUrl);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_VERBOSE, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $response = curl_exec($ch);
        curl_close($ch);

        return $response;
        
    }   

    public function getPollutionIndex($lon, $lat){
        $apiKey = "1f5cb8219df8e3067dd65255da42dd9f";
        $googleApiUrl = "https://api.openweathermap.org/data/2.5/air_pollution?lat=".$lat."&lon=".$lon."&appid=".$apiKey;

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_URL, $googleApiUrl);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_VERBOSE, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $response = curl_exec($ch);
        curl_close($ch);

        return $response;
    }

    public function name(){
        return [
            '#markup' => $this->t("Raj"),
        ];
    }
}
