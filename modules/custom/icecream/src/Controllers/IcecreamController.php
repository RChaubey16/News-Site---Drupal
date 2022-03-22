<?php

namespace Drupal\icecream\Controllers;

use \Drupal\user\Entity\User;

class IcecreamController {

    public function home(){
        $configTest = \Drupal::config('icecream.settings');
        $message = $configTest->get('message');
        $current_user = \Drupal::currentUser();
        $account = User::load($current_user->id())->getCacheTags();
        // $cache = \Drupal::cache()->getCacheTags();
        dump($account);
        return [
            '#markup' => "Hey, $message Well done!",
        ];
    }

}
