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

CREATE TABLE equipe(
   id_equipe VARCHAR(50),
   nom_equipe VARCHAR(50),
   PRIMARY KEY(id_equipe)
);

CREATE TABLE reservation(
   id_reservation INT,
   date_reservation DATE,
   heure_debut TIME,
   duree TIME,
   id_equipe VARCHAR(50) NOT NULL,
   id_court INT NOT NULL,
   PRIMARY KEY(id_reservation),
   UNIQUE(id_equipe),
   FOREIGN KEY(id_equipe) REFERENCES equipe(id_equipe),
   FOREIGN KEY(id_court) REFERENCES courts(id_court)
);

CREATE TABLE adhere(
   id_user INT,
   id_club INT,
   role_adherent VARCHAR(50) NOT NULL,
   PRIMARY KEY(id_user, id_club),
   FOREIGN KEY(id_user) REFERENCES utilisateur(id_user),
   FOREIGN KEY(id_club) REFERENCES club(id_club)
);

CREATE TABLE joue_avec(
   id_user INT,
   id_equipe VARCHAR(50),
   role VARCHAR(50),
   PRIMARY KEY(id_user, id_equipe),
   FOREIGN KEY(id_user) REFERENCES utilisateur(id_user),
   FOREIGN KEY(id_equipe) REFERENCES equipe(id_equipe)
);
