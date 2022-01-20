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
        $messenger->addMessage('Weather city accepted' . $this->city['city']);
    }

    public function build()
    {

        $config = $this->getConfiguration();
        $city = $config['weather_block_mode']['city'];

        $result = \Drupal::service('weather.service')->getWeather($city);
        $weatherData = json_decode($result);

        return [
            "#markup" => " " . $weatherData->name . " " . $weatherData->main->temp . " °C",

        ];
    }
}
