-------------------------------------------------------------
-- create table : privatemessage
-------------------------------------------------------------
CREATE TABLE `privatemessage` (
  `msgkey` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `msgvalue` text NOT NULL DEFAULT ' ',
  `msgexpireat` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`msgkey`),
  UNIQUE KEY `keyexpireat` (`msgkey`,`msgexpireat`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-------------------------------------------------------------
-- create stored procedure : delete all expired messages
-------------------------------------------------------------
DELIMITER //
CREATE PROCEDURE DeleteExpiredMessages()
BEGIN
    DELETE FROM privatemessage WHERE msgexpireat < NOW();
END //
DELIMITER ;

-------------------------------------------------------------
-- create event : all 30 seconds, delete expired messages
-------------------------------------------------------------
CREATE EVENT delete_expired_messages
ON SCHEDULE EVERY 30 SECOND
DISABLE ON SLAVE
DO CALL DeleteExpiredMessages();
