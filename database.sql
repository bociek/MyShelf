drop database `MyShelf`;
create database `apka`;
use `apka`;
show tables;

create table `users` (
`id` int not null auto_increment,
`firstname` varchar(128) not null,
`lastname` varchar(128) not null,
`username` varchar(128) not null,
primary key(`id`));

select * from users;

insert into users values (
'1',
'Jan',
'Kowalski',
'jan01');