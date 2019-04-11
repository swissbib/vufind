ALTER TABLE resource
  ADD COLUMN extra_metadata text DEFAULT NULL;

ALTER TABLE user
  MODIFY COLUMN cat_pass_enc varchar(255);

ALTER TABLE user_card
  MODIFY COLUMN cat_password varchar(70),
  MODIFY COLUMN cat_pass_enc varchar(255);
