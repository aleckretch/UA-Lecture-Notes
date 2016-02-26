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
	lectureDate	DATE NOT NULL,
	uploadDate	DATE NOT NULL,
	courseID	int NOT NULL,
	userID		int NOT NULL,
	FOREIGN KEY (courseID)
	REFERENCES Course(id)
	ON DELETE CASCADE,
	FOREIGN KEY (userID)
	REFERENCES Users(id)
	ON DELETE RESTRICT
);

DROP TABLE IF EXISTS Admin;
CREATE TABLE Admin
(
	userID		int NOT NULL primary key,
	FOREIGN KEY (userID)
	REFERENCES Users(id)
	ON DELETE CASCADE	
);

INSERT INTO Users( id, username ) VALUES( 1, 'djenkins1' );
INSERT INTO Users( id, username ) VALUES( 2, 'akretch' );
INSERT INTO Users( id, username ) VALUES( 3, 'alexanderyee' );

INSERT INTO Admin( userID ) VALUES( 1 );
INSERT INTO Admin( userID ) VALUES( 3 );

INSERT INTO Course( id, name, semester, instructor ) VALUES( 1 , 'CSC 337' , 'Fall 2015' , 'Rick Mercer' );
INSERT INTO Course( id, name, semester, instructor ) VALUES( 2 , 'CSC 337' , 'Spring 2016' , 'Mark Fischer' );
INSERT INTO Course( id, name, semester, instructor ) VALUES( 3 , 'CSC 473' , 'Spring 2016' , 'Eric Anson' );
INSERT INTO Account( userID, courseID , accountType ) VALUES( 1 , 1 , 'instructor' );
INSERT INTO Account( userID, courseID , accountType ) VALUES( 1 , 2 , 'uploader' );
INSERT INTO Account( userID, courseID , accountType ) VALUES( 2 , 1 , 'uploader' );
INSERT INTO Account( userID, courseID , accountType ) VALUES( 3 , 1 , 'instructor' );
INSERT INTO Notes( id, filetype, filename, lectureDate, uploadDate, courseID, userID) VALUES( 1, "pdf", "test", CURDATE(), CURDATE(), 3, 1);
INSERT INTO Notes( id, filetype, filename, lectureDate, uploadDate, courseID, userID) VALUES( 2, "pdf", "test", CURDATE(), CURDATE(), 3, 1);
INSERT INTO Notes( id, filetype, filename, lectureDate, uploadDate, courseID, userID) VALUES( 3, "pdf", "test", CURDATE(), CURDATE(), 3, 1);
INSERT INTO Notes( id, filetype, filename, lectureDate, uploadDate, courseID, userID) VALUES( 4, "pdf", "test", CURDATE(), CURDATE(), 3, 1);
