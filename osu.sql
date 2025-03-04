-- Table Clients
CREATE TABLE Clients (
    client_id INT PRIMARY KEY AUTO_INCREMENT,
    nom VARCHAR(255),
    prenom VARCHAR(255),
    commentaires TEXT
);

-- Table Tables (les tables physiques du restaurant)
CREATE TABLE Tables (
    table_id INT PRIMARY KEY AUTO_INCREMENT,
    numero_table INT
);

-- Table Commandes
CREATE TABLE Commandes (
    commande_id INT PRIMARY KEY AUTO_INCREMENT,
    table_id INT,
    client_id INT,
    date_commande TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    statut ENUM('En attente', 'En cours', 'Servie', 'Payée') NOT NULL DEFAULT 'En attente',
    FOREIGN KEY (table_id) REFERENCES Tables(table_id) ON DELETE CASCADE,
    FOREIGN KEY (client_id) REFERENCES Clients(client_id) ON DELETE CASCADE
);

-- Table Boissons
CREATE TABLE Boissons (
    boisson_id INT PRIMARY KEY AUTO_INCREMENT,
    nom VARCHAR(255),
    prix DECIMAL(10,2),
    image LONGBLOB NULL,
    description TEXT NULL
);

-- Table Plats
CREATE TABLE Plats (
    plat_id INT PRIMARY KEY AUTO_INCREMENT,
    nom VARCHAR(255),
    prix DECIMAL(10,2),
    image LONGBLOB NULL,
    description TEXT NULL
);

-- Table Desserts
CREATE TABLE Desserts (
    dessert_id INT PRIMARY KEY AUTO_INCREMENT,
    nom VARCHAR(255),
    prix DECIMAL(10,2),
    image LONGBLOB NULL,
    description TEXT NULL
);

-- Insertion des plats
INSERT INTO Plats (nom, prix, description) VALUES
('Nems au Poulet', 5.00, 'Petits rouleaux frits avec poulet, vermicelles, et légumes.'),
('Raviolis Vapeur', 6.00, 'Raviolis farcis au porc et crevettes, cuits à la vapeur.'),
('Salade de Papaye', 7.00, 'Salade fraîche avec papaye verte, carottes, cacahuètes, et sauce maison.'),
('Pho au Boeuf', 10.00, 'Bouillon parfumé avec nouilles de riz, boeuf finement tranché, herbes fraîches.'),
('Pho Poulet', 9.00, 'Soupe traditionnelle au poulet et nouilles de riz.'),
('Riz Cantonais', 8.00, 'Riz sauté avec porc, crevettes, et légumes.'),
('Poulet au Curry', 12.00, 'Poulet mijoté dans une sauce au curry et lait de coco, servi avec riz.'),
('Nouilles Légumes', 8.00, 'Nouilles de riz sautées avec assortiment de légumes frais.'),
('Nouilles Boeuf', 13.00, 'Nouilles de riz sautées avec du boeuf sauté.'),
('Nouilles Canard', 14.00, 'Nouilles de riz sautées avec du canard.'),
('Nouilles Porc', 11.00, 'Nouilles de riz sautées avec du porc.'),
('Nouilles Tofu', 9.00, 'Nouilles de riz sautées avec du tofu fumé.'),
('Nouilles Oeufs', 9.00, 'Nouilles de riz sautées avec des oeufs.'),
('Nouilles Crevettes', 12.00, 'Nouilles de riz sautées avec des crevettes sauce piquante.'),
('Nouilles Poulet', 10.00, 'Nouilles de riz sautées avec du poulet.'),
('Nouilles Saumon', 12.00, 'Nouilles de riz sautées avec du saumon frais.'),
('Bo Bun Végétarien', 15.00, 'Vermicelles de riz avec légumes croquants, cacahuètes, et herbes.'),
('Tofu Sauté au Gingembre', 9.00, 'Tofu sauté avec gingembre, légumes, et sauce soja.'),
('Riz aux Légumes', 7.00, 'Riz sauté aux légumes de saison et sauce légère.');

-- Insertion des desserts
INSERT INTO Desserts (nom, prix, description) VALUES
('Perles de Coco', 4.00, 'Boules de farine de riz fourrées aux haricots rouges et noix de coco.'),
('Flan au Thé Vert', 5.00, 'Flan parfumé au thé vert, une touche sucrée et légère.');

-- Insertion des boissons
INSERT INTO Boissons (nom, prix, description) VALUES
('Thé au Jasmin', 2.00, 'Thé vert parfumé au jasmin.'),
('Smoothie Mangue', 4.00, 'Smoothie frais à la mangue.'),
('Limonade Maison', 3.00, 'Limonade maison légèrement sucrée.');

-- Insertion des tables
INSERT INTO Tables (numero_table) VALUES
(1), (2), (3), (4), (5), (6), (7), (8), (9), (10);

-- Insertion d'un client anonyme
INSERT INTO Clients (nom, prenom) VALUES 
(NULL, NULL);

-- Table de liaison Commandes_Plats (Relation N-N entre Commandes et Plats)
CREATE TABLE Commandes_Plats (
    commande_id INT,
    plat_id INT,
    quantite INT NOT NULL,
    PRIMARY KEY (commande_id, plat_id),
    FOREIGN KEY (commande_id) REFERENCES Commandes(commande_id) ON DELETE CASCADE,
    FOREIGN KEY (plat_id) REFERENCES Plats(plat_id) ON DELETE CASCADE
);

-- Table de liaison Commandes_Boissons (Relation N-N entre Commandes et Boissons)
CREATE TABLE Commandes_Boissons (
    commande_id INT,
    boisson_id INT,
    quantite INT NOT NULL,
    PRIMARY KEY (commande_id, boisson_id),
    FOREIGN KEY (commande_id) REFERENCES Commandes(commande_id) ON DELETE CASCADE,
    FOREIGN KEY (boisson_id) REFERENCES Boissons(boisson_id) ON DELETE CASCADE
);

-- Table de liaison Commandes_Desserts (Relation N-N entre Commandes et Desserts)
CREATE TABLE Commandes_Desserts (
    commande_id INT,
    dessert_id INT,
    quantite INT NOT NULL,
    PRIMARY KEY (commande_id, dessert_id),
    FOREIGN KEY (commande_id) REFERENCES Commandes(commande_id) ON DELETE CASCADE,
    FOREIGN KEY (dessert_id) REFERENCES Desserts(dessert_id) ON DELETE CASCADE
);

-- Table Addition (Stocke le total d'une commande)
CREATE TABLE Addition (
    addition_id INT PRIMARY KEY AUTO_INCREMENT,
    commande_id INT UNIQUE,  -- Une seule addition par commande
    total DECIMAL(10,2) DEFAULT 0,
    FOREIGN KEY (commande_id) REFERENCES Commandes(commande_id) ON DELETE CASCADE
);
