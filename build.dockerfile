#FROM mysql:latest
#
#ENV MYSQL_ROOT_PASSWORD=root123
#
#COPY hive.sql /docker-entrypoint-initdb.d
#
##RUN mysql -h 127.0.0.1 -u root -e MYSQL_ROOT_PASSWORD=MYSQL_ROOT_PASSWORD --port 3306 --protocol tcp
#
#EXPOSE 3306
# Use an official MySQL base image
FROM mysql:latest

# Set the MySQL root password
ENV MYSQL_ROOT_PASSWORD=root_password

# Create a database and user
ENV MYSQL_DATABASE=mydatabase
ENV MYSQL_USER=myuser
ENV MYSQL_PASSWORD=mypassword

# Specify the character set and collation
ENV MYSQL_CHARSET=utf8mb4
ENV MYSQL_COLLATION=utf8mb4_unicode_ci

# Expose the MySQL port
EXPOSE 3306

# Optionally, add a script to initialize the database (e.g., import SQL dump)
 COPY hive.sql /docker-entrypoint-initdb.d/

# (Optional) Health check for container readiness
HEALTHCHECK --interval=30s --timeout=10s CMD mysqladmin ping -u${MYSQL_USER} -p${MYSQL_PASSWORD}

# Start MySQL server
CMD ["mysqld"]