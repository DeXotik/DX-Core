ALTER TABLE `levels` ADD `auto` BOOLEAN NOT NULL DEFAULT FALSE AFTER `difficulty`, ADD `demon` BOOLEAN NOT NULL DEFAULT FALSE AFTER `auto`;
ALTER TABLE `levels` CHANGE `featured` `featured` BOOLEAN NOT NULL DEFAULT FALSE;
ALTER TABLE `levels` CHANGE `deleted` `deleted` BOOLEAN NOT NULL DEFAULT FALSE;

ALTER TABLE `users` ADD `icon` INT(5) NOT NULL DEFAULT '0' AFTER `token`, ADD `color1` INT(5) NOT NULL DEFAULT '0' AFTER `icon`, ADD `color2` INT(5) NOT NULL DEFAULT '0' AFTER `color1`, ADD `stars` INT(10) NOT NULL DEFAULT '0' AFTER `color2`, ADD `demons` INT(10) NOT NULL DEFAULT '0' AFTER `stars`, ADD `creatorPoints` INT(5) NOT NULL DEFAULT '0' AFTER `demons`;
ALTER TABLE `users` ADD `scoreBan` BOOLEAN NOT NULL DEFAULT FALSE AFTER `IP`;