<?php
//define("DBMS", "Oracle");
define("DBMS_SAFE", false);//PgSqlSafe
define("DBMS_SIAN", false);//PgSqlSian
define("DBMS", "MySql");
define("DBMS_SPID", "MySql");
if (DEV) {    
    if(DBMS == "MySql"){
        define ("MYSQL_HOST", "mysql");
        //define ("MYSQL_HOST", "localhost");
        define ("MYSQL_PORT","3306");
        define ("DB_SERVER", MYSQL_HOST.";port=".MYSQL_PORT);        
        //define ("DB_SERVER", MYSQL_HOST);        
        define ("DB_NAME", "real_assets");
        define ("DB_USER", "root");
        define ("DB_PASS", "root");
    }
    if(DBMS_SPID == "MySql"){
        define ("MYSQL_HOST_SPID", "mysql");
        //define ("PGSQL_HOST", "localhost");
        define ("MYSQL_PORT_SPID","3306");
        define ("DB_SERVER_SPID", MYSQL_HOST_SPID.";port=".MYSQL_PORT_SPID);        
        define ("DB_NAME_SPID", "real_assets");
        define ("DB_USER_SPID", "root");
        define ("DB_PASS_SPID", "root");
    }

    
} else {    
    if(DBMS_SPID == "MySql"){
        define ("MYSQL_HOST", "mysql");
        //define ("MYSQL_HOST", "localhost");
        define ("MYSQL_PORT","3306");
        define ("DB_SERVER", MYSQL_HOST.";port=".MYSQL_PORT);
        //define ("DB_SERVER", MYSQL_HOST);
        define ("DB_NAME", "real_assets");
        define ("DB_USER", "root");
        define ("DB_PASS", "root");
    }
}
?>