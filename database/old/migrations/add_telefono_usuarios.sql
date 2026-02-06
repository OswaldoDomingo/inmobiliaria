-- Migration: Add telefono field to usuarios table
-- Date: 2025-12-11
-- Description: Adds phone number field to users table to display commercial/coordinator contact information

ALTER TABLE usuarios 
ADD COLUMN telefono VARCHAR(25) DEFAULT NULL 
AFTER email;
