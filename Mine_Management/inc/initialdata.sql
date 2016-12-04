CREATE TABLE terrains (
  id int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(55) NOT NULL,
  image varchar(255) NOT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY name_UNIQUE (`name`)
) ENGINE innodb;
CREATE TABLE materials (
  id int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(55) NOT NULL,
  image varchar(255) NOT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY name_UNIQUE (`name`)
) ENGINE innodb;
CREATE TABLE planets (
  id int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(55) NOT NULL,
  width int(11) unsigned NOT NULL,
  height int(11) unsigned NOT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY name_UNIQUE (`name`)
) ENGINE innodb;
CREATE TABLE planetterrains (
  refid int(11) NOT NULL AUTO_INCREMENT,
  planetid int(11) NOT NULL,
  terrainid int(11) NOT NULL,
  x int(11) NOT NULL DEFAULT '0',
  y int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (refid),
  KEY PLANET (planetid),
  KEY TERRAIN (terrainid),
  CONSTRAINT PLANET FOREIGN KEY (planetid) REFERENCES planets (id) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT TERRAIN FOREIGN KEY (terrainid) REFERENCES terrains (id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE innodb;
CREATE TABLE deposits (
  id int(11) NOT NULL AUTO_INCREMENT,
  planetid int(11) NOT NULL,
  terrainid int(11) NOT NULL,
  materialid int(11) NOT NULL,
  quantity int(11) NOT NULL,
  PRIMARY KEY (id),
  KEY MATERIAL_idx (materialid),
  KEY TERRAIN_idx (terrainid),
  KEY PLANET_idx (planetid),
  KEY QUANTITY (quantity),
  CONSTRAINT MATERIAL2 FOREIGN KEY (materialid) REFERENCES materials (id) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT PLANET2 FOREIGN KEY (planetid) REFERENCES planets (id) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT TERRAIN2 FOREIGN KEY (terrainid) REFERENCES planetterrains (refid) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE innodb;
INSERT INTO terrains (`name`, image) VALUES ('Cave','http://img.swcombine.com//galaxy/terrains/n/terrain.gif');
INSERT INTO terrains (`name`, image) VALUES ('Crater','http://img.swcombine.com//galaxy/terrains/i/terrain.gif');
INSERT INTO terrains (`name`, image) VALUES ('Desert','http://img.swcombine.com//galaxy/terrains/b/terrain.gif');
INSERT INTO terrains (`name`, image) VALUES ('Forest','http://img.swcombine.com//galaxy/terrains/c/terrain.gif');
INSERT INTO terrains (`name`, image) VALUES ('Gas Giant','http://img.swcombine.com//galaxy/terrains/o/terrain.gif');
INSERT INTO terrains (`name`, image) VALUES ('Glacier','http://img.swcombine.com//galaxy/terrains/k/terrain.gif');
INSERT INTO terrains (`name`, image) VALUES ('Grassland','http://img.swcombine.com//galaxy/terrains/f/terrain.gif');
INSERT INTO terrains (`name`, image) VALUES ('Jungle','http://img.swcombine.com//galaxy/terrains/d/terrain.gif');
INSERT INTO terrains (`name`, image) VALUES ('Mountain','http://img.swcombine.com//galaxy/terrains/l/terrain.gif');
INSERT INTO terrains (`name`, image) VALUES ('Ocean','http://img.swcombine.com//galaxy/terrains/g/terrain.gif');
INSERT INTO terrains (`name`, image) VALUES ('River','http://img.swcombine.com//galaxy/terrains/h/terrain.gif');
INSERT INTO terrains (`name`, image) VALUES ('Rock','http://img.swcombine.com//galaxy/terrains/j/terrain.gif');
INSERT INTO terrains (`name`, image) VALUES ('Swamp','http://img.swcombine.com//galaxy/terrains/e/terrain.gif');
INSERT INTO terrains (`name`, image) VALUES ('Volcanic','http://img.swcombine.com//galaxy/terrains/m/terrain.gif');
INSERT INTO materials (`name`, image) VALUES ('Quantum','http://img.swcombine.com//materials/1/main.jpg'),
('Meleenium','http://img.swcombine.com//materials/2/main.jpg'),
('Ardanium','http://img.swcombine.com//materials/3/main.jpg'),
('Rudic','http://img.swcombine.com//materials/4/main.jpg'),
('Ryll','http://img.swcombine.com//materials/5/main.jpg'),
('Duracrete','http://img.swcombine.com//materials/6/main.jpg'),
('Alazhi','http://img.swcombine.com//materials/6/main.jpg'),
('Laboi','http://img.swcombine.com//materials/8/main.jpg'),
('Adegan','http://img.swcombine.com//materials/9/main.jpg'),
('Rockivory','http://img.swcombine.com//materials/10/main.jpg'),
('Tibannagas','http://img.swcombine.com//materials/11/main.jpg'),
('Nova','http://img.swcombine.com//materials/12/main.jpg'),
('Varium','http://img.swcombine.com//materials/13/main.jpg'),
('Varmigio','http://img.swcombine.com//materials/13/main.jpg'),
('Lommite','http://img.swcombine.com//materials/15/main.jpg'),
('Hibridium','http://img.swcombine.com//materials/16/main.jpg'),
('Durelium','http://img.swcombine.com//materials/17/main.jpg'),
('Lowickan','http://img.swcombine.com//materials/18/main.jpg'),
('Vertex','http://img.swcombine.com//materials/19/main.jpg'),
('Berubian','http://img.swcombine.com//materials/20/main.jpg'),
('Bacta','http://img.swcombine.com//materials/21/main.jpg');