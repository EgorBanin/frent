use `frent`;

drop table if exists `users`;
create table `users` (
	`id` int unsigned not null auto_increment,
	`login` varchar(255) binary not null,
	`loginHash` binary(16) not null comment 'raw md5',
	`passwordHash` binary(64) not null comment 'raw sha512',
	`lastVisitTime` int unsigned not null default 0,
	`lastVisitIP` int unsigned not null default 0,
	`active` bool not null default false,
	primary key (`id`),
	unique key `loginHash` (`loginHash`)
)
engine InnoDB
comment 'Пользователи';