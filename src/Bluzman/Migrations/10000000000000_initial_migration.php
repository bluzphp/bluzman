<?php

use Phinx\Migration\AbstractMigration;

class InitialMigration extends AbstractMigration
{
    /**
     * Change Method.
     *
     * More information on this method is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-change-method
     *
     * Uncomment this method if you would like to use it.
     *
    public function change()
    {
    }
     */

    /**
     * Migrate Up.
     */
    public function up()
    {

        $this->query(
            " CREATE TABLE acl_privileges
                (
                  roleId INT UNSIGNED NOT NULL,
                  module VARCHAR(32) NOT NULL,
                  privilege VARCHAR(32) NOT NULL
                );
                CREATE TABLE acl_roles
                (
                  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
                  name VARCHAR(255) NOT NULL,
                  created TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NULL,
                  PRIMARY KEY ( id, name )
                );
                CREATE TABLE acl_users_roles
                (
                  userId BIGINT UNSIGNED NOT NULL,
                  roleId INT UNSIGNED NOT NULL,
                  PRIMARY KEY ( userId, roleId )
                );
                CREATE TABLE auth
                (
                  userId BIGINT UNSIGNED NOT NULL,
                  provider VARCHAR(64) NOT NULL,
                  foreignKey VARCHAR(255) NOT NULL,
                  token VARCHAR(64) NOT NULL,
                  tokenSecret VARCHAR(64) NOT NULL,
                  tokenType CHAR(8) NOT NULL,
                  created TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NULL,
                  updated TIMESTAMP,
                  PRIMARY KEY ( userId, provider )
                );
                CREATE TABLE categories
                (
                  id BIGINT UNSIGNED PRIMARY KEY NOT NULL AUTO_INCREMENT,
                  parentId BIGINT UNSIGNED,
                  name VARCHAR(255) NOT NULL,
                  alias VARCHAR(255) NOT NULL,
                  created TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NULL,
                  updated TIMESTAMP,
                  `order` BIGINT UNSIGNED DEFAULT 0 NOT NULL
                );
                CREATE TABLE com_content
                (
                  id BIGINT UNSIGNED PRIMARY KEY NOT NULL AUTO_INCREMENT,
                  settingsId INT UNSIGNED NOT NULL,
                  foreignKey INT UNSIGNED NOT NULL,
                  userId BIGINT UNSIGNED NOT NULL,
                  parentId BIGINT UNSIGNED,
                  content LONGTEXT,
                  created TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NULL,
                  updated TIMESTAMP,
                  status CHAR(7) DEFAULT 'active' NOT NULL
                );
                CREATE TABLE com_settings
                (
                  id INT UNSIGNED PRIMARY KEY NOT NULL AUTO_INCREMENT,
                  alias VARCHAR(255) NOT NULL,
                  options LONGTEXT,
                  created TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NULL,
                  updated TIMESTAMP,
                  countPerPage SMALLINT DEFAULT 10 NOT NULL,
                  relatedTable VARCHAR(64)
                );
                CREATE TABLE media
                (
                  id BIGINT PRIMARY KEY NOT NULL AUTO_INCREMENT,
                  userId BIGINT UNSIGNED NOT NULL,
                  module VARCHAR(24) DEFAULT 'users' NOT NULL,
                  title LONGTEXT,
                  type VARCHAR(24),
                  file VARCHAR(255),
                  preview VARCHAR(255),
                  created TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NULL,
                  updated TIMESTAMP
                );
                CREATE TABLE options
                (
                  namespace VARCHAR(64) DEFAULT 'default' NOT NULL,
                  `key` VARCHAR(255) NOT NULL,
                  value LONGTEXT NOT NULL,
                  description LONGTEXT,
                  created TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NULL,
                  updated TIMESTAMP,
                  PRIMARY KEY ( `key`, namespace )
                );
                CREATE TABLE pages
                (
                  id INT UNSIGNED PRIMARY KEY NOT NULL AUTO_INCREMENT,
                  title LONGTEXT NOT NULL,
                  alias VARCHAR(255) NOT NULL,
                  content LONGTEXT,
                  keywords LONGTEXT,
                  description LONGTEXT,
                  created TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NULL,
                  updated TIMESTAMP,
                  userId BIGINT UNSIGNED
                );
                CREATE TABLE users
                (
                  id BIGINT UNSIGNED PRIMARY KEY NOT NULL AUTO_INCREMENT,
                  login VARCHAR(255),
                  email VARCHAR(255),
                  created TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NULL,
                  updated TIMESTAMP,
                  status CHAR(8) DEFAULT 'disabled' NOT NULL
                );
                CREATE TABLE users_actions
                (
                  userId BIGINT UNSIGNED NOT NULL,
                  code VARCHAR(32) NOT NULL,
                  action CHAR(11) NOT NULL,
                  params LONGTEXT,
                  created TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NULL,
                  expired TIMESTAMP DEFAULT '0000-00-00 00:00:00' NOT NULL,
                  PRIMARY KEY ( userId, code )
                );
                ALTER TABLE acl_privileges ADD FOREIGN KEY ( roleId ) REFERENCES acl_roles ( id ) ON DELETE CASCADE ON UPDATE CASCADE;
                CREATE UNIQUE INDEX UNIQUE_access ON acl_privileges ( roleId, module, privilege );
                CREATE INDEX FK_roles ON acl_privileges ( roleId );
                CREATE UNIQUE INDEX UNIQUE_role ON acl_roles ( name );
                ALTER TABLE acl_users_roles ADD FOREIGN KEY ( roleId ) REFERENCES acl_roles ( id ) ON DELETE CASCADE ON UPDATE CASCADE;
                ALTER TABLE acl_users_roles ADD FOREIGN KEY ( userId ) REFERENCES users ( id ) ON DELETE CASCADE ON UPDATE CASCADE;
                CREATE INDEX FK_users ON acl_users_roles ( userId );
                CREATE INDEX FK_roles ON acl_users_roles ( roleId );
                ALTER TABLE auth ADD FOREIGN KEY ( userId ) REFERENCES users ( id ) ON DELETE CASCADE ON UPDATE CASCADE;
                CREATE INDEX FK_users ON auth ( userId );
                ALTER TABLE categories ADD FOREIGN KEY ( parentId ) REFERENCES categories ( id ) ON DELETE CASCADE ON UPDATE CASCADE;
                CREATE UNIQUE INDEX UNIQUE_alias ON categories ( parentId, alias );
                CREATE INDEX FK_parentId ON categories ( parentId );
                ALTER TABLE com_content ADD FOREIGN KEY ( parentId ) REFERENCES com_content ( id ) ON DELETE CASCADE ON UPDATE CASCADE;
                ALTER TABLE com_content ADD FOREIGN KEY ( settingsId ) REFERENCES com_settings ( id ) ON DELETE CASCADE ON UPDATE CASCADE;
                ALTER TABLE com_content ADD FOREIGN KEY ( userId ) REFERENCES users ( id ) ON DELETE CASCADE ON UPDATE CASCADE;
                CREATE INDEX comments_target ON com_content ( settingsId, foreignKey );
                CREATE INDEX FK_users ON com_content ( userId );
                CREATE INDEX FK_parentId ON com_content ( parentId );
                CREATE UNIQUE INDEX UNIQUE_alias ON com_settings ( alias );
                ALTER TABLE media ADD FOREIGN KEY ( userId ) REFERENCES users ( id ) ON DELETE CASCADE ON UPDATE CASCADE;
                CREATE INDEX FK_users ON media ( userId );
                ALTER TABLE pages ADD FOREIGN KEY ( userId ) REFERENCES users ( id ) ON DELETE CASCADE ON UPDATE CASCADE;
                CREATE UNIQUE INDEX UNIQUE_alias ON pages ( alias );
                CREATE INDEX FK_users ON pages ( userId );
                CREATE UNIQUE INDEX UNIQUE_login ON users ( login );
                ALTER TABLE users_actions ADD FOREIGN KEY ( userId ) REFERENCES users ( id ) ON DELETE CASCADE ON UPDATE CASCADE;
                CREATE UNIQUE INDEX UNIQUE_action ON users_actions ( userId, action );
                CREATE INDEX FK_users ON users_actions ( userId );
                "
        );


        $this->query("

          INSERT INTO `users` (`login`, `email`, `created`, `updated`, `status`)
            VALUES
                ('system',NULL,'2012-11-09 07:37:58',NULL,'disabled'),
                ('admin',NULL,'2012-11-09 07:38:41',NULL,'active');

          INSERT INTO `acl_roles` (`id`, `name`, `created`)
            VALUES
                (1,'admin','2012-11-09 07:37:31'),
                (2,'member','2012-11-09 07:37:37'),
                (3,'guest','2012-11-09 07:37:44');
          INSERT INTO `acl_users_roles` (`userId`, `roleId`)
            VALUES
                (1,1);

          INSERT INTO `acl_privileges` (`roleId`, `module`, `privilege`)
            VALUES
              (3,'users','ViewProfile'),
              (2,'media','Upload'),
              (2,'users','ViewProfile'),
              (1,'acl','Edit'),
              (1,'acl','View'),
              (1,'cache','Management'),
              (1,'dashboard','Dashboard'),
              (1,'media','Management'),
              (1,'media','Upload'),
              (1,'categories','Management'),
              (1,'options','Management'),
              (1,'pages','Management'),
              (1,'system','Info'),
              (1,'users','Management'),
              (1,'users','ViewProfile');

          INSERT INTO `auth` (`userId`, `provider`, `foreignKey`, `token`, `tokenSecret`, `tokenType`, `created`)
            VALUES
                (1,'equals','admin','f9705d72d58b2a305ab6f5913ba60a61','secretsalt','access','2012-11-09 07:40:46');

        ");
    }

    /**
     * Migrate Down.
     */
    public function down()
    {
        $this->query("
            SET FOREIGN_KEY_CHECKS=0;

            DROP TABLE users_actions;
            DROP TABLE pages;
            DROP TABLE options;
            DROP TABLE media;
            DROP TABLE com_settings;
            DROP TABLE com_content;
            DROP TABLE categories;
            DROP TABLE auth;
            DROP TABLE acl_privileges;
            DROP TABLE acl_users_roles;
            DROP TABLE users;
            DROP TABLE acl_roles;
            DROP TABLE IF EXISTS phinxLog;
            SET FOREIGN_KEY_CHECKS=1;
        ");
    }
}