CREATE TABLE Pictures
(
  id			int NOT NULL AUTO_INCREMENT,
  name 			varchar(255) NOT NULL,
  imageFormat 		varchar(7) NOT NULL,
  dateCreated 		DATE NOT NULL,
  dateAdded		DATE	NOT NULL,
  dateSeen		DATE	NOT NULL,
  description		varchar(255) DEFAULT '',
  PRIMARY KEY (id)
);

CREATE TABLE Albums
(
  id			int NOT NULL AUTO_INCREMENT,
  name 			varchar(255) NOT NULL,
  dateCreated 		DATE NOT NULL,
  dateModified		DATE	NOT NULL,
  description		varchar(255) DEFAULT '',
  PRIMARY KEY (id)
);

CREATE TABLE Tags
(
  id			int NOT NULL AUTO_INCREMENT,
  pId			int NOT NULL,
  name 			varchar(31) NOT NULL,
  description		varchar(255) NOT NULL,
  FOREIGN KEY	(pId)	REFERENCES Pictures(id),
  PRIMARY KEY (id)
);

CREATE TABLE EXIFItems
(
  id			int NOT NULL AUTO_INCREMENT,
  pId			int NOT NULL,
  tag			varchar(255) NOT NULL,
  value			varchar(255) NOT NULL,
  FOREIGN KEY	(pId)	REFERENCES Pictures(id),
  PRIMARY KEY (id)
);