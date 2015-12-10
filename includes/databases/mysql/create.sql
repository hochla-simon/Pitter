CREATE TABLE users
(
id            int NOT NULL AUTO_INCREMENT,
firstName        varchar(50) NOT NULL,
lastName        varchar(50) NOT NULL,
email            varchar(254) NOT NULL UNIQUE,
password        varchar(255) NOT NULL,
registered		int,
enabled			int(1) DEFAULT 0,
isAdmin		int(1) DEFAULT 0,
PRIMARY KEY (id)
);

CREATE TABLE images
(
id            int NOT NULL AUTO_INCREMENT,
ownerId        int NOT NULL,
name             varchar(255) DEFAULT '',
filename        varchar(255) NOT NULL,
extension        varchar(7) NOT NULL,
created         DATE NOT NULL,
description        varchar(255) DEFAULT '',
PRIMARY KEY (id)
);

CREATE TABLE albums
(
id            int NOT NULL AUTO_INCREMENT,
parentAlbumId        int DEFAULT '1',
ownerId        int NOT NULL,    
name             varchar(255) NOT NULL,
created        DATE NOT NULL,
modified        DATE    NOT NULL,
description        varchar(255) DEFAULT '',
PRIMARY KEY (id)
);

CREATE TABLE imagesToAlbums
(
albumId    int NOT NULL,
imageId    int NOT NULL,
positionInAlbum    int NOT NULL,
PRIMARY KEY (albumId, imageId)
);

CREATE TABLE tags
(
id            int NOT NULL AUTO_INCREMENT,
imageId        int NOT NULL,
name             varchar(31) NOT NULL,
description        varchar(255) DEFAULT '',
PRIMARY KEY (id)
);

CREATE TABLE metadata
(
id            int NOT NULL AUTO_INCREMENT,
imageId        int NOT NULL,
name            varchar(255) NOT NULL,
value            varchar(255) NOT NULL,
PRIMARY KEY (id)
);

CREATE TABLE linkToAlbums
(
id         int NOT NULL AUTO_INCREMENT,
albumId    int NOT NULL,
link       varchar(255) NOT NULL,
PRIMARY KEY (id)
);

CREATE TABLE usersToAlbums
(
albumId   int NOT NULL,
userId    int NOT NULL,
PRIMARY KEY (albumId, userId)
);

INSERT INTO albums(parentAlbumId, ownerId, name, created, modified, description) VALUES('-1', '1', 'ROOT', CURDATE(), CURDATE(), 'This is the root album.');