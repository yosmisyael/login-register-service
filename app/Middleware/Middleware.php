<?php 

namespace Yosev\Login\Management\Middleware;

interface Middleware
{
    public function before(): void;
} 
