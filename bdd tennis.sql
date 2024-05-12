
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `tennis`
--

-- --------------------------------------------------------

--
-- Structure de la table `appartenance_club`
--

CREATE TABLE `appartenance_club` (
  `id_user` int(11) NOT NULL,
  `id_club` int(11) NOT NULL,
  `role_adherent` varchar(50) NOT NULL DEFAULT 'adherant'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `club`
--

CREATE TABLE `club` (
  `id_club` int(11) NOT NULL,
  `nom_club` varchar(50) NOT NULL,
  `ville` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `courts`
--

CREATE TABLE `courts` (
  `id_court` int(11) NOT NULL,
  `type_surface` varchar(50) DEFAULT NULL,
  `emplacement` varchar(50) DEFAULT NULL,
  `etat` tinyint(1) NOT NULL DEFAULT 1,
  `id_club` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `inscrits`
--

CREATE TABLE `inscrits` (
  `id_user` int(11) NOT NULL,
  `id_reservation` int(11) NOT NULL,
  `role` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `reservation`
--

CREATE TABLE `reservation` (
  `id_reservation` int(11) NOT NULL,
  `start_datetime` datetime DEFAULT NULL,
  `end_datetime` datetime DEFAULT NULL,
  `id_court` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `utilisateur`
--

CREATE TABLE `utilisateur` (
  `id_user` int(11) NOT NULL,
  `nom` varchar(30) NOT NULL,
  `prenom` varchar(30) NOT NULL,
  `password` varchar(50) NOT NULL,
  `email` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `appartenance_club`
--
ALTER TABLE `appartenance_club`
  ADD PRIMARY KEY (`id_user`,`id_club`),
  ADD KEY `id_club` (`id_club`);

--
-- Index pour la table `club`
--
ALTER TABLE `club`
  ADD PRIMARY KEY (`id_club`);

--
-- Index pour la table `courts`
--
ALTER TABLE `courts`
  ADD PRIMARY KEY (`id_court`),
  ADD KEY `id_club` (`id_club`);

--
-- Index pour la table `inscrits`
--
ALTER TABLE `inscrits`
  ADD PRIMARY KEY (`id_user`,`id_reservation`),
  ADD KEY `id_reservation` (`id_reservation`);

--
-- Index pour la table `reservation`
--
ALTER TABLE `reservation`
  ADD PRIMARY KEY (`id_reservation`),
  ADD KEY `id_court` (`id_court`);

--
-- Index pour la table `utilisateur`
--
ALTER TABLE `utilisateur`
  ADD PRIMARY KEY (`id_user`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `club`
--
ALTER TABLE `club`
  MODIFY `id_club` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT pour la table `courts`
--
ALTER TABLE `courts`
  MODIFY `id_court` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT pour la table `reservation`
--
ALTER TABLE `reservation`
  MODIFY `id_reservation` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT pour la table `utilisateur`
--
ALTER TABLE `utilisateur`
  MODIFY `id_user` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `appartenance_club`
--
ALTER TABLE `appartenance_club`
  ADD CONSTRAINT `appartenance_club_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `utilisateur` (`id_user`),
  ADD CONSTRAINT `appartenance_club_ibfk_2` FOREIGN KEY (`id_club`) REFERENCES `club` (`id_club`);

--
-- Contraintes pour la table `courts`
--
ALTER TABLE `courts`
  ADD CONSTRAINT `courts_ibfk_1` FOREIGN KEY (`id_club`) REFERENCES `club` (`id_club`);

--
-- Contraintes pour la table `inscrits`
--
ALTER TABLE `inscrits`
  ADD CONSTRAINT `inscrits_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `utilisateur` (`id_user`),
  ADD CONSTRAINT `inscrits_ibfk_2` FOREIGN KEY (`id_reservation`) REFERENCES `reservation` (`id_reservation`);

--
-- Contraintes pour la table `reservation`
--
ALTER TABLE `reservation`
  ADD CONSTRAINT `reservation_ibfk_1` FOREIGN KEY (`id_court`) REFERENCES `courts` (`id_court`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
