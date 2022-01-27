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

        if ($typeOfData == "weather") {
            return [
                "#markup" => " " . $weatherData->name . " " . $weatherData->main->temp . " °C",

            ];
        } elseif ($typeOfData == "pollution") {
            $lon = $weatherData->coord->lon;
            $lat = $weatherData->coord->lat;

            $result = \Drupal::service('weather.service')->getPollutionIndex($lon, $lat);            
            $pollutionData = json_decode($result);
            $rate = $pollutionData->list[0]->components->co;

            return[
                "#markup" => " " . $weatherData->name . " Pollution " . $rate . " AQI",
            ];
        }
    }
}
