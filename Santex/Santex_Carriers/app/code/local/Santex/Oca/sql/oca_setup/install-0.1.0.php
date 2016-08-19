<?php
$installer = $this;

$installer->startSetup();

$installer->run("
CREATE TABLE {$this->getTable('oca/branch')} (
  branch_id       INT UNSIGNED    NOT NULL  AUTO_INCREMENT,
  code            VARCHAR (10)    NOT NULL  DEFAULT '',
  short_name      VARCHAR (10)    NOT NULL  DEFAULT '',
  name            VARCHAR (100)   NOT NULL  DEFAULT '',
  description     VARCHAR (100)   NOT NULL  DEFAULT '',
  address_street  VARCHAR (100)   NOT NULL  DEFAULT '',
  address_number  VARCHAR (100)   NOT NULL  DEFAULT '',
  address_floor   VARCHAR (100)   NOT NULL  DEFAULT '',
  city            VARCHAR (50)    NOT NULL  DEFAULT '',
  zipcode         VARCHAR (20)    NOT NULL  DEFAULT '',
  active          BOOLEAN         NOT NULL  DEFAULT 0,
  PRIMARY KEY  (branch_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE {$this->getTable('oca/operatory')} (
  operatory_id          INT UNSIGNED  NOT NULL AUTO_INCREMENT,
  name                  VARCHAR (100) NOT NULL DEFAULT '',
  code                  VARCHAR (10)  NOT NULL DEFAULT '',
  active                BOOLEAN       NOT NULL DEFAULT 1,
  uses_idci             BOOLEAN       NOT NULL DEFAULT 0,
  pays_on_destination   BOOLEAN       NOT NULL DEFAULT 0,
  PRIMARY KEY (operatory_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8

");

$installer->endSetup();
