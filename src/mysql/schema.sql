use `frent`;

drop table if exists `sessions`;
create table `sessions` (
	`id` int unsigned not null auto_increment,
	`token` binary(20) not null,
	`description` varchar(255) not null,
	`deleted` bool not null,
	`ut` int unsigned not null,
	`data` json not null,
	primary key (`id`)
	)
engine InnoDB
comment 'Сессии';


drop table if exists `users`;
create table `users` (
	`id` int unsigned not null auto_increment,
	`login` varchar(255) binary not null,
	`loginHash` binary(16) not null comment 'raw md5',
	`passwordHash` binary(64) not null comment 'raw sha512',
	`data` json not null comment 'JSON, данные пользователя',
	`active` bool not null,
	primary key (`id`),
	unique key `loginHash` (`loginHash`)
)
engine InnoDB
comment 'Пользователи';

drop table if exists `profiles`;
create table `profiles` (
	`id` int unsigned not null auto_increment,
	`name` varchar(255) not null,
	`summary` varchar(3000) not null,
	`userId` int unsigned not null comment 'id создателя/владельца',
	`ct` int unsigned not null comment 'timestamp времени создания',
	primary key (`id`),
)
engine InnoDB
comment 'Профайлы пользователей';

drop table if exists `profile-users`;
create table `profile-users` (
	`id` int unsigned not null auto_increment,
	`profileId` int unsigned not null,
	`userId` int unsigned not null,
	primary key (`id`),
	unique key `profileId` (`profileId`),
	key `userId` (`userId`)
)
engine InnoDB
comment 'Связи профайл-пользователи';

drop table if exists `things`;
create table `things` (
	`id` int unsigned not null auto_increment,
	`profileId` int unsigned not null,
	`title` varchar(255) not null,
	`description` varchar(3000) not null,
	primary key (`id`),
	key `ownerId` (`ownerId`)
)
engine InnoDB
comment 'Вещи';