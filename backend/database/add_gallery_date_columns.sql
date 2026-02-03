-- Add upload_month and upload_year columns to gallery table
ALTER TABLE gallery 
ADD COLUMN upload_month VARCHAR(2) AFTER description,
ADD COLUMN upload_year VARCHAR(4) AFTER upload_month;

-- Update existing records to have default month and year based on created_at
UPDATE gallery 
SET 
    upload_month = MONTH(created_at),
    upload_year = YEAR(created_at)
WHERE upload_month IS NULL OR upload_year IS NULL;
