<?php
namespace Webkul\User\Models;

use Laravel\Sanctum\HasApiTokens;

class Admin extends Authenticatable implements AdminContract
{
    use HasApiTokens;
    ...
}
