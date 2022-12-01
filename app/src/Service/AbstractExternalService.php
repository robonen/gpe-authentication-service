<?php

namespace App\Service;

abstract class AbstractExternalService
{
     public function send(string $reciever, string $data)
     {
         return [
             'reciever' => $reciever,
             'data' => $data
         ];
     }
}
