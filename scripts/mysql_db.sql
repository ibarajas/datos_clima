SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';


-- -----------------------------------------------------
-- Table `captcha`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `captcha` (
  `captcha_id` BIGINT(13) UNSIGNED NOT NULL AUTO_INCREMENT,
  `captcha_time` VARCHAR(20) NOT NULL,
  `ip_address` VARCHAR(16) NOT NULL DEFAULT '0',
  `word` VARCHAR(20) NOT NULL,
  PRIMARY KEY (`captcha_id`),
  INDEX `word` (`word` ASC))
ENGINE = InnoDB
AUTO_INCREMENT = 32
DEFAULT CHARACTER SET = latin1;


-- -----------------------------------------------------
-- Table `stations`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `stations` (
  `idStation` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `city` VARCHAR(150) NOT NULL,
  `location` VARCHAR(150) NOT NULL,
  `lon` FLOAT NOT NULL,
  `lat` FLOAT NOT NULL,
  `wmo` INT(10) UNSIGNED NOT NULL,
  PRIMARY KEY (`idStation`),
  UNIQUE INDEX `wmo_UNIQUE` (`wmo` ASC))
ENGINE = InnoDB
AUTO_INCREMENT = 2
DEFAULT CHARACTER SET = latin1;


-- -----------------------------------------------------
-- Table `captures`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `captures` (
  `idCapture` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `idStation` INT(10) UNSIGNED NOT NULL,
  `dates` DATETIME NOT NULL,
  PRIMARY KEY (`idCapture`, `idStation`),
  INDEX `fk_captures_1_idx` (`idStation` ASC),
  CONSTRAINT `fk_captures_1`
    FOREIGN KEY (`idStation`)
    REFERENCES `stations` (`idStation`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
AUTO_INCREMENT = 150
DEFAULT CHARACTER SET = latin1;


-- -----------------------------------------------------
-- Table `dailysummary`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `dailysummary` (
  `idDailysummary` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `idStation` INT(10) UNSIGNED NOT NULL,
  `dates` DATETIME NOT NULL,
  `mintempm` DECIMAL(5,2) NULL DEFAULT NULL,
  `maxtempm` DECIMAL(5,2) NULL DEFAULT NULL,
  `meantempm` DECIMAL(5,2) NULL DEFAULT NULL,
  `minhumidity` DECIMAL(4,2) NULL DEFAULT NULL,
  `maxhumidity` DECIMAL(4,2) NULL DEFAULT NULL,
  `humidity` DECIMAL(4,2) NULL DEFAULT NULL,
  `minpressurem` DECIMAL(7,2) NULL DEFAULT NULL,
  `maxpressurem` DECIMAL(7,2) NULL DEFAULT NULL,
  `meanpressurem` DECIMAL(7,2) NULL DEFAULT NULL,
  `minwspdm` DECIMAL(5,2) NULL DEFAULT NULL,
  `maxwspdm` DECIMAL(5,2) NULL DEFAULT NULL,
  `meanwindspdm` DECIMAL(5,2) NULL DEFAULT NULL,
  `meanwdird` DECIMAL(5,2) NULL DEFAULT NULL,
  `precipm` DECIMAL(6,2) NULL DEFAULT NULL,
  `heatingdegreedays` DECIMAL(5,2) NULL DEFAULT NULL,
  PRIMARY KEY (`idDailysummary`, `idStation`))
ENGINE = InnoDB
AUTO_INCREMENT = 1052
DEFAULT CHARACTER SET = latin1;


-- -----------------------------------------------------
-- Table `forecasts`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `forecasts` (
  `idForecast` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `idCapture` INT(10) UNSIGNED NOT NULL,
  `idStation` INT(10) UNSIGNED NOT NULL,
  `dates` DATETIME NOT NULL,
  `temp` DECIMAL(5,2) NULL DEFAULT NULL,
  `wspd` DECIMAL(5,2) NULL DEFAULT NULL,
  `wdir` DECIMAL(5,2) NULL DEFAULT NULL,
  `humidity` DECIMAL(4,2) NULL DEFAULT NULL,
  `windchill` DECIMAL(5,2) NULL DEFAULT NULL,
  `heatindex` DECIMAL(5,2) NULL DEFAULT NULL,
  `feelslike` DECIMAL(5,2) NULL DEFAULT NULL,
  `mslp` DECIMAL(7,2) NULL DEFAULT NULL,
  `pop` DECIMAL(4,2) NULL DEFAULT NULL,
  PRIMARY KEY (`idForecast`, `idCapture`, `idStation`),
  INDEX `fk_forecasts_1_idx` (`idCapture` ASC, `idStation` ASC),
  CONSTRAINT `fk_forecasts_1`
    FOREIGN KEY (`idCapture` , `idStation`)
    REFERENCES `captures` (`idCapture` , `idStation`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
AUTO_INCREMENT = 32881
DEFAULT CHARACTER SET = latin1;


-- -----------------------------------------------------
-- Table `groups`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `groups` (
  `id` MEDIUMINT(8) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(20) NOT NULL,
  `description` VARCHAR(100) NOT NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB
AUTO_INCREMENT = 3
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `login_attempts`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `login_attempts` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `ip_address` VARCHAR(15) NOT NULL,
  `login` VARCHAR(100) NOT NULL,
  `time` INT(11) UNSIGNED NULL DEFAULT NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `samples`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `samples` (
  `idSample` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `idStation` INT(10) UNSIGNED NOT NULL,
  `dates` DATETIME NOT NULL,
  `tempm` DECIMAL(5,2) NULL DEFAULT NULL,
  `windchillm` DECIMAL(5,2) NULL DEFAULT NULL,
  `heatindexm` DECIMAL(5,2) NULL DEFAULT NULL,
  `hum` DECIMAL(4,2) NULL DEFAULT NULL,
  `wspdm` DECIMAL(5,2) NULL DEFAULT NULL,
  `wgustm` DECIMAL(5,2) NULL DEFAULT NULL,
  `wdird` DECIMAL(5,2) NULL DEFAULT NULL,
  `pressurem` DECIMAL(7,2) NULL DEFAULT NULL,
  `precipm` DECIMAL(6,2) NULL DEFAULT NULL,
  PRIMARY KEY (`idSample`, `idStation`),
  INDEX `fk_samples_1_idx` (`idStation` ASC),
  CONSTRAINT `fk_samples_1`
    FOREIGN KEY (`idStation`)
    REFERENCES `stations` (`idStation`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
AUTO_INCREMENT = 29860
DEFAULT CHARACTER SET = latin1;


-- -----------------------------------------------------
-- Table `users`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `users` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `ip_address` VARCHAR(15) NOT NULL,
  `username` VARCHAR(100) NOT NULL,
  `password` VARCHAR(255) NOT NULL,
  `salt` VARCHAR(255) NULL DEFAULT NULL,
  `email` VARCHAR(100) NOT NULL,
  `activation_code` VARCHAR(40) NULL DEFAULT NULL,
  `forgotten_password_code` VARCHAR(40) NULL DEFAULT NULL,
  `forgotten_password_time` INT(11) UNSIGNED NULL DEFAULT NULL,
  `remember_code` VARCHAR(40) NULL DEFAULT NULL,
  `created_on` INT(11) UNSIGNED NOT NULL,
  `last_login` INT(11) UNSIGNED NULL DEFAULT NULL,
  `active` TINYINT(1) UNSIGNED NULL DEFAULT NULL,
  `first_name` VARCHAR(50) NULL DEFAULT NULL,
  `last_name` VARCHAR(50) NULL DEFAULT NULL,
  `company` VARCHAR(100) NULL DEFAULT NULL,
  `phone` VARCHAR(20) NULL DEFAULT NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB
AUTO_INCREMENT = 21
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `users_groups`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `users_groups` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` INT(11) UNSIGNED NOT NULL,
  `group_id` MEDIUMINT(8) UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `uc_users_groups` (`user_id` ASC, `group_id` ASC),
  INDEX `fk_users_groups_users1_idx` (`user_id` ASC),
  INDEX `fk_users_groups_groups1_idx` (`group_id` ASC),
  CONSTRAINT `fk_users_groups_groups1`
    FOREIGN KEY (`group_id`)
    REFERENCES `groups` (`id`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_users_groups_users1`
    FOREIGN KEY (`user_id`)
    REFERENCES `users` (`id`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION)
ENGINE = InnoDB
AUTO_INCREMENT = 60
DEFAULT CHARACTER SET = utf8;



INSERT INTO `groups` (`id`, `name`, `description`) VALUES
     (1,'admin','Administrator'),
     (2,'members','General User');

INSERT INTO `users` (`id`, `ip_address`, `username`, `password`, `salt`, `email`, `activation_code`, `forgotten_password_code`, `created_on`, `last_login`, `active`, `first_name`, `last_name`, `company`, `phone`) VALUES
     ('1','127.0.0.1','administrator','zxoo15D0ddSPF6PAwYc.fu9ed9b5d53ca80abe52','','admin@admin.com','',NULL,'1268889823','1268889823','1', 'Admin','istrator','ADMIN','0');

INSERT INTO `users_groups` (`id`, `user_id`, `group_id`) VALUES
     (1,1,1),
     (2,1,2);

INSERT INTO `stations` (`idStation`, `city`, `location`, `lon`, `lat`, `wmo`) VALUES 
     ('0', 'Test_city', 'Test_location', '-99', '99', '99999'),
     ('1', 'Tucuman', 'Tucuman Aerodrome', '-65.1049', '-26.8409', '87121');

SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
