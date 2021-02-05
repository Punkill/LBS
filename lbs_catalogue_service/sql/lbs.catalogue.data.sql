SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

INSERT INTO `categorie` (`id`, `nom`, `description`) VALUES
(1,	'bio',	'sandwichs ingrédients bio et locaux'),
(2,	'végétarien',	'sandwichs végétariens - peuvent contenir des produits laitiers'),
(3,	'traditionnel',	'sandwichs traditionnels : jambon, pâté, poulet etc ..'),
(4,	'chaud',	'sandwichs chauds : américain, burger, '),
(5,	'veggie',	'100% Veggie'),
(16,	'world',	'Tacos, nems, burritos, nos sandwichs du monde entier');

INSERT INTO `sand2cat` (`sand_id`, `cat_id`) VALUES
(4,	3),
(4,	4),
(5,	3),
(5,	1),
(6,	4),
(6,	16);

INSERT INTO `sandwich` (`id`, `nom`, `description`, `type_pain`, `img`, `prix`) VALUES
(4,	'le bucheron',	'un sandwich de bucheron : frites, fromage, saucisse, steack, lard grillé, mayo',	'baguette campagne',	NULL, 6.00),
(5,	'jambon-beurre',	'le jambon-beurre traditionnel, avec des cornichons',	'baguette',	NULL, 5.25),
(6,	'fajitas poulet',	'fajitas au poulet avec ses tortillas de mais, comme à Puebla',	'tortillas',	NULL, 6.50),
(7,	'le forestier',	'un bon sandwich au gout de la forêt',	'pain complet',	NULL, 6.75);

