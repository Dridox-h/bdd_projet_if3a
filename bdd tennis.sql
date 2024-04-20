CREATE TABLE utilisateur(
   id_user INT,
   nom VARCHAR(30) NOT NULL,
   prenom VARCHAR(30) NOT NULL,
   password VARCHAR(50) NOT NULL,
   email VARCHAR(50) NOT NULL,
   PRIMARY KEY(id_user)
);

CREATE TABLE club(
   id_club INT,
   nom_club VARCHAR(50) NOT NULL,
   PRIMARY KEY(id_club)
);

CREATE TABLE courts(
   id_court INT,
   type_surface VARCHAR(50),
   emplacement VARCHAR(50),
   id_club INT NOT NULL,
   PRIMARY KEY(id_court),
   FOREIGN KEY(id_club) REFERENCES club(id_club)
);

CREATE TABLE reservation(
   id_reservation INT,
   date_reservation DATE,
   heure_debut TIME,
   duree TIME,
   id_court INT NOT NULL,
   PRIMARY KEY(id_reservation),
   FOREIGN KEY(id_court) REFERENCES courts(id_court)
);

CREATE TABLE adhere(
   id_user_1 INT,
   id_club_1 INT,
   id_club VARCHAR(50),
   id_user VARCHAR(50),
   role_adherent VARCHAR(50) NOT NULL,
   PRIMARY KEY(id_user_1, id_club_1),
   FOREIGN KEY(id_user_1) REFERENCES utilisateur(id_user),
   FOREIGN KEY(id_club_1) REFERENCES club(id_club)
);

CREATE TABLE reserve(
   id_user INT,
   role VARCHAR(50),
   id_reservation INT NOT NULL,
   PRIMARY KEY(id_user),
   FOREIGN KEY(id_user) REFERENCES utilisateur(id_user),
   FOREIGN KEY(id_reservation) REFERENCES reservation(id_reservation)
);
