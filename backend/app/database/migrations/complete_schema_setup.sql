-- This is a comprehensive SQL schema for direct database setup
-- Run this after running initial Laravel migrations

-- Update users table to add our custom fields
ALTER TABLE users ADD COLUMN IF NOT EXISTS organization_id BIGINT UNSIGNED AFTER id;
ALTER TABLE users ADD COLUMN IF NOT EXISTS role_id BIGINT UNSIGNED AFTER organization_id;
ALTER TABLE users ADD COLUMN IF NOT EXISTS phone VARCHAR(20) AFTER email;
ALTER TABLE users ADD COLUMN IF NOT EXISTS profile_photo VARCHAR(255) AFTER phone;
ALTER TABLE users ADD COLUMN IF NOT EXISTS language ENUM('en', 'si') DEFAULT 'en' AFTER profile_photo;
ALTER TABLE users ADD COLUMN IF NOT EXISTS status ENUM('active', 'inactive', 'blocked') DEFAULT 'active' AFTER language;
ALTER TABLE users ADD COLUMN IF NOT EXISTS phone_verified_at TIMESTAMP NULL AFTER email_verified_at;
ALTER TABLE users ADD COLUMN IF NOT EXISTS last_login_at TIMESTAMP NULL AFTER phone_verified_at;
ALTER TABLE users ADD COLUMN IF NOT EXISTS settings JSON AFTER last_login_at;
ALTER TABLE users ADD COLUMN IF NOT EXISTS deleted_at TIMESTAMP NULL AFTER updated_at;
ALTER TABLE users ADD COLUMN IF NOT EXISTS created_by BIGINT UNSIGNED AFTER deleted_at;
ALTER TABLE users ADD COLUMN IF NOT EXISTS updated_by BIGINT UNSIGNED AFTER created_by;

-- Add indexes to users
CREATE INDEX IF NOT EXISTS idx_users_organization ON users(organization_id);
CREATE INDEX IF NOT EXISTS idx_users_role ON users(role_id);
CREATE INDEX IF NOT EXISTS idx_users_phone ON users(phone);
CREATE INDEX IF NOT EXISTS idx_users_status ON users(status);
