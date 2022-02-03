<?php

namespace Drupal\icecream\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;

/**
 *  @Block(
 *      id = "icecream_weather_module",
 *      admin_label = @Translation("Weather custom block"),
 *      category = @Translation("custom"),
 *  )
 */

class WeatherBlock extends BlockBase
{

    public $city;

    /**
     * {@inheritdoc}
     */
    public function blockForm($form, FormStateInterface $form_state)
    {
        $form = parent::blockForm($form, $form_state);

        $config = $this->getConfiguration();
        $city = $config['weather_block_mode']['city'];

        $options = array(
            'weather' => $this->t('temperature'),
            'pollution' => $this->t('pollution'),
        );

        $form['weather_or_pollution'] = [
            '#type' => 'radios',
            '#title' => $this->t('Choose on of the following'),
            '#description' => $this->t('Will display pollution rate or current weather of the city'),
            '#options' => $options,
            '#default_value' => $options['pollution'],
        ];

        $form['Weather'] = [
            '#type' => 'textfield',
            '#title' => $this->t('City'),
            '#description' => $this->t('Enter the city to get the weather.'),
            '#name' => "city",
            '#value' => $city,
        ];


        return $form;
    }

    /**
     * {@inheritdoc}
     */
    public function blockSubmit($form, FormStateInterface $form_state)
    {

        $this->configuration['weather_block_mode'] = $form_state->getUserInput('city');

        $this->city = $form_state->getUserInput('city');
        $messenger = \Drupal::messenger();
        // $messenger->addMessage('Weather city accepted' . $this->city['city']);
    }

    public function build()
    {

        $config = $this->getConfiguration();
        $city = $config['weather_block_mode']['city'];
        $typeOfData = $config['weather_block_mode']['settings']['weather_or_pollution'];

        $result = \Drupal::service('weather.service')->getWeather($city);
        $weatherData = json_decode($result);
        $forecast = strtolower($weatherData->weather[0]->main);

        // Icon based on forecast
        if ($forecast == "clear") {
            // $icon__class = "<i class='fas fa-sun'></i>";
            $icon__class = "fa-sun";
        } elseif ($forecast == "clouds") {
            $icon__class = "fa-cloud";
        } elseif ($forecast == 'mist') {
            // $icon__class =  "<i class='fas fa-snowflake'></i>";
            $icon__class =  "fa-snowflake";
        } elseif ($forecast == 'rainy') {
            // $icon__class =  "<i class='fas fa-cloud-rain'></i>";
            $icon__class =  "fa-cloud-rain";
        } else {
            $icon__class = "fa-cloud-sun";
        }

        if ($typeOfData == "weather") {
            // return [
            //     "#markup" => "<p> " . $weatherData->name . " " . $weatherData->main->temp . " °C " . $icon__class . "
            //     </p>",
            //     '#attributes' => [
            //         'class' => ['weather__block'],
            //     ],

            // ];

            return [
                "#theme" => "weather_pollution",
                "#city" => $weatherData->name,
                "#data" => $weatherData->main->temp . " °C ",
                "#icon" => $icon__class,
                "#class" => "weather__red",
            ];
        } elseif ($typeOfData == "pollution") {
            $lon = $weatherData->coord->lon;
            $lat = $weatherData->coord->lat;

            $result = \Drupal::service('weather.service')->getPollutionIndex($lon, $lat);
            $pollutionData = json_decode($result);
            $rate = round($pollutionData->list[0]->components->pm10);

            // background color based pollution index
            if ($rate < 40) {
                $pollution_class = "good__green";
            } elseif ($rate > 40 && $rate < 100) {
                $pollution_class = "moderate__yellow";
            } elseif ($rate > 100) {
                $pollution_class = "poor__red";
            }

            // return [
            //     "#markup" => " " . $weatherData->name . " Pollution " . $rate . " AQI",
            //     '#attributes' => [
            //         'class' => [$pollution_class],
            //     ],
            // ];

            return [
                "#theme" => "weather_pollution",
                "#city" => $weatherData->name,
                "#data" => $rate . " AQI",
                "#class" => $pollution_class,
            ];
        }
    }
}
