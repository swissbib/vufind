--
-- Add one field to table `pura_user`
--
ALTER TABLE `pura_user`
  ADD COLUMN `is_member_education_institution` BOOLEAN NOT NULL DEFAULT FALSE AFTER `last_account_extension_request` ;