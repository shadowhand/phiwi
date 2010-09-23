<?php

include 'phiwi.php';

Phiwi::proto('person', array(
		'init' => function($self, $name) {
			$self->name = $name;
		},
		'hello' => function($self) {
			return "Hello, I am {$self->name}!\n";
		},
		'meet' => function($self, $other) {
			return "Nice to meet you {$other->name}, I'm {$self->name}!\n";
		},
	));

$tom  = Phiwi::factory('person', 'Tom');
$mark = Phiwi::factory('person', 'Mark');

echo $tom->hello();
echo $mark->hello();

echo $tom->meet($mark);

Phiwi::proto('animal', array(
		'init' => function($self, $name) {
			$self->name = $name;
		},
		'speak' => function($self) {
			throw new BadMethodCallException("Animals don't talk");
		},
	));

Phiwi::extend('dog', 'animal', array(
		'speak' => function($self) {
			return "WOOF!\n";
		},
	));

$spot = Phiwi::factory('dog', 'Spot');

echo $spot->speak();

Phiwi::extend('cat', 'animal', array(
		'speak' => function($self) {
			return "meow!\n";
		},
	));

$fluffy = Phiwi::factory('cat', 'Fluffy');

echo $fluffy->speak();

Phiwi::proto('house', array(
		'init' => function($self) {
			$self->has_door = TRUE;
		},
		'move' => function($self, $location) {
			return "Calling the movers to go to {$location}!\n";
		},
	));

Phiwi::proto('boat', array(
		'init' => function($self) {
			$self->has_sail = TRUE;
		},
		'move' => function($self, $location) {
			return "Setting sail for {$location}!\n";
		},
	));

$house = Phiwi::factory('house')
	->mixin('boat');

echo "Does my house have a sail? ", empty($house->has_sail) ? "No :(" : "Yes :D", "\n";
echo "Does my house have a door? ", empty($house->has_door) ? "No :(" : "Yes :D", "\n";

echo $house->move('Jamaica');
