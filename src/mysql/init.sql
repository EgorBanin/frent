drop database if exists `frent`;

create database `frent`
default character set = 'utf8'
default collate = 'utf8_general_ci';

create user 'frent'@'localhost'
identified by 'passw0rd';

grant select, insert, update, delete, lock tables
on `frent`.*
to 'frent'@'localhost';