# Reset MySQL root password (forgot password)

Run these commands **one by one**. You'll need `sudo`.

## 1. Stop MySQL

```bash
sudo systemctl stop mysql
```

## 2. Start MySQL without privilege checks (no password required)

```bash
sudo mysqld_safe --skip-grant-tables --skip-networking &
```

Wait a few seconds, then continue.

## 3. Connect as root (no password)

```bash
mysql -u root
```

## 4. In the MySQL prompt, set a new root password

```sql
FLUSH PRIVILEGES;
ALTER USER 'root'@'localhost' IDENTIFIED BY 'YOUR_NEW_ROOT_PASSWORD';
EXIT;
```

Replace `YOUR_NEW_ROOT_PASSWORD` with a strong password you will remember.

## 5. Stop the temporary MySQL process

```bash
sudo killall mysqld
```

(If it says "no process found", that's OK.)

## 6. Start MySQL normally

```bash
sudo systemctl start mysql
```

## 7. Test the new password

```bash
mysql -u root -p
```

Enter the new password. If it works, you can then create the `opapru_app7` user:

```sql
CREATE USER IF NOT EXISTS 'opapru_app7'@'127.0.0.1' IDENTIFIED BY 'Sup3rAdM!nOppApp320!!xN0vU@2026Lut1ons!';
CREATE USER IF NOT EXISTS 'opapru_app7'@'localhost' IDENTIFIED BY 'Sup3rAdM!nOppApp320!!xN0vU@2026Lut1ons!';
GRANT ALL PRIVILEGES ON opapru_hris.* TO 'opapru_app7'@'127.0.0.1';
GRANT ALL PRIVILEGES ON opapru_hris.* TO 'opapru_app7'@'localhost';
GRANT ALL PRIVILEGES ON oppapru_logs.* TO 'opapru_app7'@'127.0.0.1';
GRANT ALL PRIVILEGES ON oppapru_logs.* TO 'opapru_app7'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

---

**If `mysqld_safe` is not in your PATH**, try:

```bash
sudo /usr/bin/mysqld_safe --skip-grant-tables --skip-networking &
```

**If you get "Access denied" after reset:**  
Some installs use `auth_socket` for root. In step 4 use:

```sql
FLUSH PRIVILEGES;
ALTER USER 'root'@'localhost' IDENTIFIED WITH mysql_native_password BY 'YOUR_NEW_ROOT_PASSWORD';
EXIT;
```
