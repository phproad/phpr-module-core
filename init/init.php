<?php

// Core events
//

Phpr::$events = new Core_Events();
Phpr::$events->fire_event('phpr:on_initialize');

// Init all modules
//

Core_Module_Manager::get_modules();
