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

CREATE TABLE appartenance_club(
   id_user INT,
   id_club INT,
   role_adherent VARCHAR(50) NOT NULL,
   PRIMARY KEY(id_user, id_club),
   FOREIGN KEY(id_user) REFERENCES utilisateur(id_user),
   FOREIGN KEY(id_club) REFERENCES club(id_club)
);

CREATE TABLE Inscrits(
   id_user INT,
   id_reservation INT,
   role VARCHAR(50),
   PRIMARY KEY(id_user, id_reservation),
   FOREIGN KEY(id_user) REFERENCES utilisateur(id_user),
   FOREIGN KEY(id_reservation) REFERENCES reservation(id_reservation)
);
