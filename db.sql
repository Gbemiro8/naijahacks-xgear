CREATE DATABASE team_foobaa
USE team_foobaa;
CREATE TABLE registration (id int not null auto_increment PRIMARY KEY, 
name varchar(50) not null,
email varchar(50) not null,
password varchar(20) not null,
confirm_password varchar(20) not null
);