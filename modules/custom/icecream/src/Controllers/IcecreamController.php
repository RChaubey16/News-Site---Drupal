<?php

namespace Drupal\icecream\Controllers;

class IcecreamController {

    public function home(){
        return [
            '#markup' => "Hey, Well done!",
        ];
    }

}