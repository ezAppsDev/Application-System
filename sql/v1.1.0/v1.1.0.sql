ALTER TABLE `users` ADD `discord_id` TEXT NULL AFTER `usergroup`;

ALTER TABLE `users` ADD `avatar` TEXT NULL AFTER `discord_id`;

ALTER TABLE `settings` ADD `wh_app_created` ENUM('true','false') NOT NULL DEFAULT 'true' AFTER `discord_webhook`;

ALTER TABLE `settings` ADD `wh_app_accepted` ENUM('true','false') NOT NULL DEFAULT 'true' AFTER `discord_webhook`;

ALTER TABLE `settings` ADD `wh_app_declined` ENUM('true','false') NOT NULL DEFAULT 'true' AFTER `discord_webhook`;

ALTER TABLE `usergroups` ADD `view_apps` ENUM('true','false') NOT NULL DEFAULT 'false' AFTER `super_admin`;

ALTER TABLE `usergroups` ADD `review_apps` ENUM('true','false') NOT NULL DEFAULT 'false' AFTER `view_apps`, ADD `view_users` ENUM('true','false') NOT NULL DEFAULT 'false' AFTER `review_apps`, ADD `view_usergroups` ENUM('true','false') NOT NULL DEFAULT 'false' AFTER `view_users`, ADD `edit_users` ENUM('true','false') NOT NULL DEFAULT 'false' AFTER `view_usergroups`, ADD `edit_usergroups` ENUM('true','false') NOT NULL DEFAULT 'false' AFTER `edit_users`;

ALTER TABLE `settings` ADD `theme` VARCHAR(64) NOT NULL DEFAULT 'default' AFTER `app_accept_message`;
