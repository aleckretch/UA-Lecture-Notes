DROP DATABASE IF EXISTS LECTURE;
CREATE DATABASE LECTURE   
DEFAULT CHARACTER SET utf8
DEFAULT COLLATE utf8_general_ci;
USE LECTURE;

/*
	This holds the netid for the users.
	The username has to be unique.

	id: 		a unique key particular to each user
	username:	the netid for a particular user, also unique
*/
DROP TABLE IF EXISTS Users;
CREATE TABLE Users
(
	id		int NOT NULL auto_increment primary key,
	username	varchar(100) NOT NULL,
	UNIQUE( username )
);

/*
	This holds information on each course.

	id:		a unique key particular to each course
	name:		the name of the course
	semester: 	the semester and year for the course
	instructor:	the name of the instructor for the course
*/
DROP TABLE IF EXISTS Course;
CREATE TABLE Course
(
	id		int NOT NULL auto_increment primary key,
	name		varchar(200) not null,
	semester	varchar(200) not null,
	instructor	varchar(200) NOT NULL
);

/*
	This holds account types for a particular user account for a particular course.
	The userID and courseID together form a unique identifier for the row.
	IMPORTANT: Not every user will have an account here, 
		if they do not then they are just regular users that can search courses/download files
		they cannot upload files or create courses
	
	userID:		the id of the user for this account
	courseID:	the id of the course that this account has access to
	accountType:	the type of account this is: i.e instructor,uploader...
*/
DROP TABLE IF EXISTS Account;
CREATE TABLE Account
(
	userID		int NOT NULL,
	courseID	int NOT NULL,
	accountType	varchar(100) NOT NULL,
	FOREIGN KEY (userID)
	REFERENCES Users(id)
	ON DELETE CASCADE,	
	FOREIGN KEY (courseID)
	REFERENCES Course(id)
	ON DELETE CASCADE,
	PRIMARY KEY( userID, courseID )
);

/*
	This holds information on each of the lecture notes files uploaded.
	
	id:		the unique identifier for each file
	filetype:	the extension of the file
	filename:	the preferred name of the file(NOT THE NAME OF THE FILE ON THE SERVER)
	lectureDate:	the date of the lecture that the notes are for
	uploadDate:	the date that the file was uploaded
	courseID:	the id of the course that the file is for
	userID:		the id of the user that uploaded the file
*/
DROP TABLE IF EXISTS Notes;
CREATE TABLE Notes
(
	id		int NOT NULL auto_increment primary key,
	filetype	varchar(100) NOT NULL,
	filename	varchar(100) NOT NULL,
	lectureDate	datetime NOT NULL,
	uploadDate	datetime NOT NULL,
	courseID	int NOT NULL,
	userID		int NOT NULL,
	FOREIGN KEY (courseID)
	REFERENCES Course(id)
	ON DELETE CASCADE,
	FOREIGN KEY (userID)
	REFERENCES Users(id)
	ON DELETE RESTRICT
);


