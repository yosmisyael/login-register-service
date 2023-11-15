<?php 

namespace Yosev\Login\Management\App 
{
    function header(string $value)
    {
        echo $value;
    }
}

namespace Yosev\Login\Management\Service
{
    function setcookie(string $name, string $value)
    {
    echo "$name: $value";
    }
}
 
