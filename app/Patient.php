<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Patient extends Model
{
  protected $connection = 'hosxp';

  protected $table = 'patient';
}
