<?php

/**
 * Core events
 */

Phpr::$events = new Core_Events();

/**
 * Init all modules
 */

Core_Module_Manager::find_modules();
