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
    prix DECIMAL(10,2)
);

-- Table Plats
CREATE TABLE Plats (
    plat_id INT PRIMARY KEY AUTO_INCREMENT,
    nom VARCHAR(255),
    prix DECIMAL(10,2)
);

-- Table Desserts
CREATE TABLE Desserts (
    dessert_id INT PRIMARY KEY AUTO_INCREMENT,
    nom VARCHAR(255),
    prix DECIMAL(10,2)
);

INSERT INTO Plats (nom, prix) VALUES
('Nems au Poulet', 5.00),
('Raviolis Vapeur', 6.00),
('Salade de Papaye', 7.00),
('Pho au Boeuf', 10.00),
('Pho Poulet', 9.00),
('Riz Cantonais', 8.00),
('Poulet au Curry', 12.00),
('Nouilles Légumes', 8.00),
('Nouilles Boeuf', 13.00),
('Nouilles Canard', 14.00),
('Nouilles Porc', 11.00),
('Nouilles Tofu', 9.00),
('Nouilles Oeufs', 9.00),
('Nouilles Crevettes', 12.00),
('Nouilles Poulet', 10.00),
('Nouilles Saumon', 12.00),
('Bo Bun Végétarien', 15.00),
('Tofu Sauté au Gingembre', 9.00),
('Riz aux Légumes', 7.00);

INSERT INTO Desserts (nom, prix) VALUES
('Perles de Coco', 4.00),
('Flan au The Vert', 5.00);

INSERT INTO Boissons (nom, prix) VALUES
('The au Jasmin', 2.00),
('Smoothie Mangue', 4.00),
('Limonade Maison', 3.00);

INSERT INTO Tables (numero_table) VALUES
(1),
(2),
(3),
(4),
(5),
(6),
(7),
(8),
(9),
(10);

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
