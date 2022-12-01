# Production Setup

# Local Development Setup

1. Copy `.env.exmaple` to root with `.env` name.
   1. Fill the database connection fields.
        1. Set database connection to tenant
        2. Fill the tenant connection fields:
           > In Tenant-Connection database name and username must be same
           > 
           > TENANCY_DATABASE=kct_tnt_m_db
           > 
           > TENANCY_USERNAME=kct_tnt_m_db
2. Perform Database Tenant Management
   1. Create DATABASE
   ``` 
   CREATE DATABASE IF NOT EXISTS kct_tnt_m_db; 
   ```
   2. Create USER
   ```
    CREATE USER IF NOT EXISTS kct_tnt_m_db@localhost IDENTIFIED BY 'kct_tnt_m_db';
   ```
   3. Grant the permissions to tenant user
   ```
   GRANT ALL PRIVILEGES ON *.* TO kct_tnt_m_db@localhost WITH GRANT OPTION; 
   ```
   
3. Install the dependencies: `composer install`
