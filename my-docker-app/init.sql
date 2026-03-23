USE haumonstersDB;

CREATE TABLE IF NOT EXISTS playerstbl (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    password VARCHAR(255) NOT NULL
);

INSERT INTO playerstbl (name, password) VALUES ('PlayerOne', 'pass1234');
INSERT INTO playerstbl (name, password) VALUES ('ShadowKnight', 'knight@99');
