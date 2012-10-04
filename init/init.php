<?php

// Core events
//

Phpr::$events = new Core_Events();
Phpr::$events->fire_event('core:on_initialize');

// Init all modules
//

Core_Module_Manager::find_modules();

// Add notify types to class loader
// 

Phpr::$class_loader->add_module_directory('notify_types');
