<?php

use Illuminate\Support\Facades\Schedule;

// Schedule the news:fetch command to run every hour.
Schedule::command('news:fetch')->hourly();
