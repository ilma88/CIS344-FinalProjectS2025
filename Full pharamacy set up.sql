-- 1. Create Database
CREATE DATABASE IF NOT EXISTS pharmacy_portal_db;
USE pharmacy_portal_db;

-- 2. Tables
CREATE TABLE IF NOT EXISTS Users (
    userId INT NOT NULL AUTO_INCREMENT,
    userName VARCHAR(45) NOT NULL UNIQUE,
    contactInfo VARCHAR(200),
    userType ENUM('pharmacist', 'patient') NOT NULL,
    PRIMARY KEY (userId)
);

CREATE TABLE IF NOT EXISTS Medications (
    medicationId INT NOT NULL AUTO_INCREMENT,
    medicationName VARCHAR(45) NOT NULL,
    dosage VARCHAR(45) NOT NULL,
    manufacturer VARCHAR(100),
    PRIMARY KEY (medicationId)
);

CREATE TABLE IF NOT EXISTS Prescriptions (
    prescriptionId INT NOT NULL AUTO_INCREMENT,
    userId INT NOT NULL,
    medicationId INT NOT NULL,
    prescribedDate DATETIME NOT NULL,
    dosageInstructions VARCHAR(200),
    quantity INT NOT NULL,
    refillCount INT DEFAULT 0,
    PRIMARY KEY (prescriptionId),
    FOREIGN KEY (userId) REFERENCES Users(userId),
    FOREIGN KEY (medicationId) REFERENCES Medications(medicationId)
);

CREATE TABLE IF NOT EXISTS Inventory (
    inventoryId INT NOT NULL AUTO_INCREMENT,
    medicationId INT NOT NULL,
    quantityAvailable INT NOT NULL,
    lastUpdated DATETIME NOT NULL,
    PRIMARY KEY (inventoryId),
    FOREIGN KEY (medicationId) REFERENCES Medications(medicationId)
);

CREATE TABLE IF NOT EXISTS Sales (
    saleId INT NOT NULL AUTO_INCREMENT,
    prescriptionId INT NOT NULL,
    saleDate DATETIME NOT NULL,
    quantitySold INT NOT NULL,
    saleAmount DECIMAL(10, 2) NOT NULL,
    PRIMARY KEY (saleId),
    FOREIGN KEY (prescriptionId) REFERENCES Prescriptions(prescriptionId)
);

-- 3. View
CREATE OR REPLACE VIEW MedicationInventoryView AS
SELECT 
    m.medicationName, 
    m.dosage, 
    m.manufacturer, 
    i.quantityAvailable
FROM Medications m
JOIN Inventory i ON m.medicationId = i.medicationId;

-- 4. Stored Procedures
DELIMITER //
CREATE PROCEDURE AddOrUpdateUser(
    IN p_userName VARCHAR(45),
    IN p_contactInfo VARCHAR(200),
    IN p_userType ENUM('pharmacist', 'patient')
)
BEGIN
    DECLARE existingUserId INT;

    SELECT userId INTO existingUserId FROM Users WHERE userName = p_userName;

    IF existingUserId IS NOT NULL THEN
        UPDATE Users 
        SET contactInfo = p_contactInfo, userType = p_userType
        WHERE userId = existingUserId;
    ELSE
        INSERT INTO Users (userName, contactInfo, userType)
        VALUES (p_userName, p_contactInfo, p_userType);
    END IF;
END //
DELIMITER ;

DELIMITER //
CREATE PROCEDURE ProcessSale(
    IN p_prescriptionId INT,
    IN p_quantitySold INT
)
BEGIN
    DECLARE medId INT;
    DECLARE saleAmount DECIMAL(10,2);

    SELECT medicationId INTO medId FROM Prescriptions WHERE prescriptionId = p_prescriptionId;

    SET saleAmount = p_quantitySold * 10.00;

    UPDATE Inventory 
    SET quantityAvailable = quantityAvailable - p_quantitySold,
        lastUpdated = NOW()
    WHERE medicationId = medId;

    INSERT INTO Sales (prescriptionId, saleDate, quantitySold, saleAmount)
    VALUES (p_prescriptionId, NOW(), p_quantitySold, saleAmount);
END //
DELIMITER ;

-- 5. Trigger
DELIMITER //
CREATE TRIGGER AfterPrescriptionInsert
AFTER INSERT ON Prescriptions
FOR EACH ROW
BEGIN
    UPDATE Inventory
    SET quantityAvailable = quantityAvailable - NEW.quantity,
        lastUpdated = NOW()
    WHERE medicationId = NEW.medicationId;

    IF (SELECT quantityAvailable FROM Inventory WHERE medicationId = NEW.medicationId) < 10 THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Warning: Stock is low!';
    END IF;
END //
DELIMITER ;

-- 6. Sample Data
INSERT INTO Users (userName, contactInfo, userType) VALUES
('pharma_john', 'john@example.com', 'pharmacist'),
('alice_patient', 'alice@example.com', 'patient'),
('bob_patient', 'bob@example.com', 'patient');

INSERT INTO Medications (medicationName, dosage, manufacturer) VALUES
('Amoxicillin', '500mg', 'Pfizer'),
('Ibuprofen', '200mg', 'Bayer'),
('Paracetamol', '500mg', 'GlaxoSmithKline');

INSERT INTO Inventory (medicationId, quantityAvailable, lastUpdated) VALUES
(1, 50, NOW()),
(2, 100, NOW()),
(3, 75, NOW());

INSERT INTO Prescriptions (userId, medicationId, prescribedDate, dosageInstructions, quantity) VALUES
(2, 1, NOW(), 'Take twice a day', 10),
(3, 2, NOW(), 'One tablet after meals', 20),
(2, 3, NOW(), 'Three times daily', 15);

INSERT INTO Sales (prescriptionId, saleDate, quantitySold, saleAmount) VALUES
(1, NOW(), 10, 100.00),
(2, NOW(), 20, 200.00),
(3, NOW(), 15, 150.00);