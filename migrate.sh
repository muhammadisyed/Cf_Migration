#!/bin/bash
drush sql-drop -y
drush sql-cli < latest.sql

drush cim -y

drush pmu cf_migration migrate migrate_conditions migrate_plus -y
drush cr
drush en cf_migration migrate migrate_conditions migrate_plus -y

drush ms
