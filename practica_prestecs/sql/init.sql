-- Creació de la taula de préstecs
CREATE TABLE IF NOT EXISTS prestecs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    alumne VARCHAR(100) NOT NULL,
    equip_id VARCHAR(50) NOT NULL,
    data_prestec TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    estat ENUM('Prestat', 'Retornat') DEFAULT 'Prestat'
);

-- Inserció de dades d'exemple per comprovar que la web funciona
INSERT INTO prestecs (alumne, equip_id, estat) VALUES 
('Marc Torrent', 'LAPTOP-ASIX-01', 'Prestat'),
('Laia Garcia', 'LAPTOP-ASIX-05', 'Prestat'),
('Jordi Muns', 'TABLET-03', 'Retornat'),
('Marta Rovira', 'LAPTOP-ASIX-12', 'Prestat');