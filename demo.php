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
