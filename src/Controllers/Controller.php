<?php

namespace App\Controllers;

interface Controller
{
    public bool $needLogin { get; }
}